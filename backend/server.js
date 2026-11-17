// Plan to Plate backend - zero dependencies, just Node 18+  (run: node server.js)
const http = require('http'), fs = require('fs'), path = require('path'), crypto = require('crypto');
const PORT = process.env.PORT || 3000;
const SECRET = process.env.SECRET || 'change-this-secret';
const ROOT = path.join(__dirname, '..', 'frontend');
const DB = path.join(__dirname, 'data');
const MIME = { '.html': 'text/html', '.css': 'text/css', '.js': 'text/javascript', '.jpg': 'image/jpeg', '.png': 'image/png', '.json': 'application/json', '.svg': 'image/svg+xml' };

const read = (f, d) => { try { return JSON.parse(fs.readFileSync(path.join(DB, f), 'utf8')); } catch { return d; } };
const write = (f, v) => fs.writeFileSync(path.join(DB, f), JSON.stringify(v, null, 2));
const json = (res, code, obj) => { res.writeHead(code, { 'Content-Type': 'application/json' }); res.end(JSON.stringify(obj)); };
const readBody = req => new Promise(ok => { let b = ''; req.on('data', c => { b += c; if (b.length > 1e6) req.destroy(); }); req.on('end', () => { try { ok(JSON.parse(b || '{}')); } catch { ok({}); } }); });

// ---- auth helpers (scrypt password hashing + signed token) ----
const hashPw = (pw, salt) => crypto.scryptSync(pw, salt, 32).toString('hex');
const sign = user => { const p = Buffer.from(JSON.stringify({ user, exp: Date.now() + 7 * 864e5 })).toString('base64url'); return p + '.' + crypto.createHmac('sha256', SECRET).update(p).digest('base64url'); };
function who(req) {
  const t = (req.headers.authorization || '').replace('Bearer ', ''), [p, s] = t.split('.');
  if (!p || !s) return null;
  const ok = crypto.createHmac('sha256', SECRET).update(p).digest('base64url');
  if (s.length !== ok.length || !crypto.timingSafeEqual(Buffer.from(s), Buffer.from(ok))) return null;
  const d = JSON.parse(Buffer.from(p, 'base64url')); return d.exp > Date.now() ? d.user : null;
}
function addUser(users, name, pw) { const salt = crypto.randomBytes(8).toString('hex'); users[name] = { salt, hash: hashPw(pw, salt) }; }

// first run: create demo user chef / chef123
const users0 = read('users.json', null);
if (!users0) { const u = {}; addUser(u, 'chef', 'chef123'); write('users.json', u); }

// ---- API ----
async function api(req, res, url) {
  const m = req.method, p = url.pathname;
  if (m === 'GET' && p === '/api/recipes') {
    const q = (url.searchParams.get('q') || '').toLowerCase(), cat = url.searchParams.get('category');
    let list = [...read('recipes.json', []), ...read('user-recipes.json', [])];
    if (cat) list = list.filter(r => r.cat === cat);
    if (q) list = list.filter(r => (r.t + ' ' + r.ing).toLowerCase().includes(q));
    return json(res, 200, list);
  }
  if (m === 'GET' && /^\/api\/recipes\/\d+$/.test(p)) {
    const r = read('recipes.json', []).find(x => x.id === +p.split('/').pop());
    return r ? json(res, 200, r) : json(res, 404, { error: 'Recipe not found' });
  }
  if (m === 'POST' && (p === '/api/register' || p === '/api/login')) {
    const { username, password } = await readBody(req), users = read('users.json', {});
    if (!username || !password || password.length < 4) return json(res, 400, { error: 'Username and a password of 4+ characters required' });
    if (p === '/api/register') {
      if (users[username]) return json(res, 409, { error: 'Username already taken' });
      addUser(users, username, password); write('users.json', users);
      return json(res, 201, { token: sign(username), user: username });
    }
    const u = users[username];
    if (!u || hashPw(password, u.salt) !== u.hash) return json(res, 401, { error: 'Wrong username or password' });
    return json(res, 200, { token: sign(username), user: username });
  }
  const user = who(req);
  if (!user) return json(res, 401, { error: 'Please log in' });
  const favs = read('favorites.json', {});
  if (p === '/api/favorites' && m === 'GET') return json(res, 200, favs[user] || []);
  if (/^\/api\/favorites\/\d+$/.test(p)) {
    const id = +p.split('/').pop(); let list = favs[user] || [];
    if (m === 'POST') list = [...new Set([...list, id])];
    else if (m === 'DELETE') list = list.filter(x => x !== id);
    else return json(res, 405, { error: 'Method not allowed' });
    favs[user] = list; write('favorites.json', favs); return json(res, 200, list);
  }
  if (p === '/api/my-recipes' && m === 'POST') {
    const b = await readBody(req);
    if (!b.t || !b.ing || !b.steps) return json(res, 400, { error: 't, ing and steps are required' });
    const all = read('user-recipes.json', []);
    const r = { id: 1000 + all.length, owner: user, t: String(b.t).slice(0, 80), cat: b.cat || 'Main', prep: +b.prep || 10, cook: +b.cook || 10, serv: +b.serv || 2, ing: String(b.ing), steps: String(b.steps) };
    all.push(r); write('user-recipes.json', all); return json(res, 201, r);
  }
  json(res, 404, { error: 'Unknown API route' });
}

// ---- static files + server ----
http.createServer(async (req, res) => {
  const url = new URL(req.url, 'http://localhost');
  try {
    if (url.pathname.startsWith('/api/')) return await api(req, res, url);
    let f = path.normalize(path.join(ROOT, url.pathname === '/' ? 'index.html' : decodeURIComponent(url.pathname)));
    if (!f.startsWith(ROOT) || !fs.existsSync(f) || fs.statSync(f).isDirectory()) { res.writeHead(404); return res.end('Not found'); }
    res.writeHead(200, { 'Content-Type': MIME[path.extname(f)] || 'application/octet-stream' });
    fs.createReadStream(f).pipe(res);
  } catch (e) { json(res, 500, { error: 'Server error' }); }
}).listen(PORT, () => console.log('Plan to Plate running at http://localhost:' + PORT));