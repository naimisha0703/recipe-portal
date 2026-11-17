# Plan to Plate

## Project layout

- `frontend/` contains the static recipe website.
- `backend/` contains the optional zero-dependency Node.js API and JSON data.
- `vercel.json` publishes the static frontend from `frontend/`.

## Run locally

Install Node.js 18 or newer, then run:

```sh
cd backend
node server.js
```

Open `http://localhost:3000`. The demo account is `chef` / `chef123`.

## Personal dashboard

Sign in or register, then choose **My dashboard** in the navigation. It shows your recipes, saved favourites, and quick recipes. Recipe ownership and favourites are stored in this browser's local storage, so they do not sync across devices.

## Deployment

Vercel serves the static frontend. The optional JSON API in `backend/` is not connected to the deployed frontend; shared accounts and server-side persistence need a hosted database and API deployment.

The matching recipe JPEG photos are stored locally in `frontend/images/`; the site does not depend on third-party image hotlinks.
