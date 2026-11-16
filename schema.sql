-- Recipe Sharing Portal | DBMS Mini Project
-- Import this file in phpMyAdmin (XAMPP) or run: mysql -u root < schema.sql
-- Demo login -> username: demo_kitchen  |  password: Demo@123

CREATE DATABASE IF NOT EXISTS recipe_portal
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE recipe_portal;

DROP TABLE IF EXISTS recipes;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS app_sessions;

CREATE TABLE app_sessions (
  session_id   VARCHAR(128) PRIMARY KEY,
  session_data MEDIUMBLOB NOT NULL,
  expires_at   INT UNSIGNED NOT NULL,
  INDEX idx_session_expiry (expires_at)
) ENGINE=InnoDB;

CREATE TABLE users (
  user_id       INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  username      VARCHAR(30)  NOT NULL UNIQUE,
  email         VARCHAR(120) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  created_at    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE categories (
  category_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name        VARCHAR(40) NOT NULL UNIQUE,
  emoji       VARCHAR(8)  NOT NULL DEFAULT '🍽️'
) ENGINE=InnoDB;

CREATE TABLE recipes (
  recipe_id    INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id      INT UNSIGNED NOT NULL,
  category_id  INT UNSIGNED NOT NULL,
  title        VARCHAR(120) NOT NULL,
  description  VARCHAR(255) NOT NULL,
  ingredients  TEXT         NOT NULL,   -- one ingredient per line
  instructions TEXT         NOT NULL,   -- one step per line
  prep_time    SMALLINT UNSIGNED NOT NULL DEFAULT 0,  -- minutes
  cook_time    SMALLINT UNSIGNED NOT NULL DEFAULT 0,  -- minutes
  servings     TINYINT  UNSIGNED NOT NULL DEFAULT 2,
  emoji        VARCHAR(8)   NOT NULL DEFAULT '🍽️',
  color        CHAR(7)      NOT NULL DEFAULT '#F4C9A0',
  image_url    VARCHAR(500) NULL,
  created_at   TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_recipe_user     FOREIGN KEY (user_id)     REFERENCES users(user_id)           ON DELETE CASCADE,
  CONSTRAINT fk_recipe_category FOREIGN KEY (category_id) REFERENCES categories(category_id)  ON DELETE RESTRICT,
  INDEX idx_recipe_title (title),
  INDEX idx_recipe_category (category_id)
) ENGINE=InnoDB;

INSERT INTO users (username, email, password_hash) VALUES ('demo_kitchen', 'demo@recipeportal.local', '$2y$10$0ib3em4aQgWqA1nYkIC8S.b7QxKQND96dFcMp2IWgt9pxfR2wSqGC');

INSERT INTO categories (name, emoji) VALUES
  ('Breakfast', '🥞'),
  ('Main Course', '🍛'),
  ('Soups & Salads', '🥗'),
  ('Snacks', '🍟'),
  ('Desserts', '🍰'),
  ('Drinks', '🥤');

INSERT INTO recipes (user_id, category_id, title, description, ingredients, instructions, prep_time, cook_time, servings, emoji, color) VALUES
  (1, 1, 'Masala Omelette', 'Soft, spiced eggs folded around onion, green chilli and coriander.', '4 eggs
1 small onion, finely chopped
1 green chilli, minced
1 small tomato, diced
2 tbsp chopped coriander
1/4 tsp turmeric
Salt to taste
1 tbsp butter or oil', 'Whisk the eggs with salt and turmeric until frothy.
Stir in onion, chilli, tomato and coriander.
Melt the butter in a non-stick pan over medium heat.
Pour in half the mixture, tilt to spread and cook until the edges set.
Fold, cook 30 seconds more and serve hot with toast.', 5, 8, 2, '🍳', '#F7D488'),
  (1, 1, 'Banana Oat Pancakes', 'Naturally sweet pancakes made with ripe bananas and rolled oats.', '2 ripe bananas
1 cup rolled oats
1 cup milk
1 egg
1 tsp baking powder
1/2 tsp cinnamon
1 tsp honey
Butter for the pan', 'Blend bananas, oats, milk, egg, baking powder, cinnamon and honey until smooth.
Rest the batter for 5 minutes so it thickens.
Heat a buttered pan on medium-low.
Pour small rounds and cook until bubbles appear, about 2 minutes.
Flip, cook another minute and serve with honey or sliced fruit.', 10, 15, 3, '🥞', '#F4C9A0'),
  (1, 1, 'Peanut Poha', 'Light, tangy flattened rice with curry leaves, peanuts and lemon.', '2 cups thick poha
1/4 cup roasted peanuts
1 onion, chopped
1 potato, finely diced
1 tsp mustard seeds
10 curry leaves
1/2 tsp turmeric
Juice of 1 lemon
2 tbsp oil
Salt and coriander', 'Rinse the poha in a colander and let it soften for 5 minutes.
Heat oil, crackle the mustard seeds, then add curry leaves and peanuts.
Add onion and potato and cook until tender.
Stir in turmeric, salt and the poha; toss gently for 2 minutes.
Finish with lemon juice and coriander.', 10, 12, 3, '🥣', '#FBE08C'),
  (1, 2, 'Paneer Butter Masala', 'Cubes of paneer in a silky tomato, butter and cream gravy.', '250 g paneer, cubed
4 ripe tomatoes, chopped
1 onion, chopped
10 cashews
2 tbsp butter
1 tsp ginger-garlic paste
1 tsp Kashmiri chilli powder
1 tsp garam masala
1 tsp kasuri methi
3 tbsp cream
Salt to taste', 'Simmer tomatoes, onion and cashews with a little water for 10 minutes, then blend smooth.
Melt the butter, add ginger-garlic paste and cook for a minute.
Pour in the puree, chilli powder and salt; simmer 8 minutes.
Add paneer, garam masala and crushed kasuri methi.
Stir in the cream, simmer 2 minutes and serve with naan or rice.', 20, 25, 4, '🍛', '#F2A65A'),
  (1, 2, 'Veg Fried Rice', 'Wok-tossed rice with crunchy vegetables and a hit of soy and garlic.', '3 cups cooked, cooled rice
1 carrot, diced
1/2 cup beans, sliced
1/2 capsicum, diced
3 spring onions, whites and greens separated
3 garlic cloves, minced
2 tbsp soy sauce
1 tsp vinegar
1/2 tsp pepper
2 tbsp oil', 'Heat the oil in a wok on high heat.
Fry garlic for 20 seconds, then add carrot and beans for 2 minutes.
Add capsicum and the spring onion whites; toss for a minute.
Add rice, soy sauce, vinegar and pepper.
Toss on high heat for 3 minutes and garnish with the spring onion greens.', 15, 10, 3, '🍚', '#E8D9A8'),
  (1, 2, 'Creamy Mushroom Pasta', 'Golden mushrooms and garlic in a glossy cream sauce over penne.', '200 g penne
250 g mushrooms, sliced
3 garlic cloves, minced
1/2 cup cream
1/2 cup pasta water
2 tbsp butter
1 tsp dried oregano
1/2 tsp chilli flakes
Grated cheese and black pepper
Salt', 'Boil the pasta in salted water until al dente and save a cup of the water.
Sauté the mushrooms in butter on high heat until golden.
Add garlic, oregano and chilli flakes for 30 seconds.
Pour in the cream and pasta water; simmer until it coats a spoon.
Toss in the pasta and cheese, season and serve.', 10, 20, 2, '🍝', '#EBC9A4'),
  (1, 2, 'Punjabi Chole', 'Spiced chickpeas simmered in a tangy onion-tomato masala.', '2 cups boiled chickpeas
2 onions, finely chopped
2 tomatoes, pureed
1 tbsp ginger-garlic paste
1 tbsp chole masala
1 tsp cumin seeds
2 tbsp oil
Salt to taste
Lemon juice and coriander', 'Heat the oil and splutter the cumin seeds.
Cook the onions until deep golden, then add ginger-garlic paste.
Add tomato puree, chole masala and salt; cook until the oil separates.
Add chickpeas with 1 cup water and simmer 20 minutes, mashing a few to thicken.
Finish with lemon juice and coriander.', 15, 35, 4, '🍲', '#E3A36B'),
  (1, 3, 'Thick Tomato Soup', 'A velvety roasted-tomato soup with a swirl of cream.', '6 ripe tomatoes
1 small onion
1 carrot
3 garlic cloves
1 tbsp butter
1 tsp sugar
1/2 tsp pepper
Salt to taste
Fresh cream to serve', 'Sauté onion, garlic and carrot in butter for 5 minutes.
Add chopped tomatoes and 1 cup water; simmer 15 minutes.
Blend until smooth and strain if you like it silky.
Return to the pot and add sugar, salt and pepper.
Serve hot with a swirl of cream.', 10, 25, 3, '🍅', '#F28B7A'),
  (1, 3, 'Strawberry Spinach Salad', 'Sweet berries, tender spinach, feta and toasted almonds.', '3 cups baby spinach
1 cup sliced strawberries
1/4 cup crumbled feta
2 tbsp toasted almonds
1 tbsp olive oil
1 tbsp balsamic vinegar
1 tsp honey
Black pepper', 'Whisk olive oil, balsamic vinegar, honey and pepper.
Toss the spinach with half the dressing.
Top with strawberries, feta and almonds.
Drizzle the rest and serve right away.', 10, 0, 2, '🥗', '#B9D9A7'),
  (1, 4, 'Crispy Veg Spring Rolls', 'Crackly golden rolls packed with ginger-scented vegetables.', '8 spring roll sheets
1 cup shredded cabbage
1 carrot, julienned
1 capsicum, sliced
2 spring onions
1 tbsp soy sauce
1 tsp grated ginger
1 tbsp flour paste for sealing
Oil for frying', 'Stir-fry the vegetables with ginger and soy for 2 minutes, then cool completely.
Place filling on a sheet, fold in the sides and roll tightly.
Seal the edge with flour paste.
Fry on medium heat until golden, about 4 minutes.
Drain and serve with chilli sauce.', 25, 15, 4, '🌯', '#F1C98B'),
  (1, 4, 'Masala French Fries', 'Double-fried potato sticks tossed in chaat masala.', '3 large potatoes
2 tbsp cornflour
1 tsp chaat masala
1/2 tsp chilli powder
1/2 tsp dried oregano
Salt to taste
Oil for frying', 'Cut the potatoes into sticks and soak in cold water for 20 minutes.
Dry well and toss with cornflour.
Fry on medium heat until pale, remove, then fry again until crisp.
Toss with salt, chaat masala, chilli powder and oregano while hot.', 10, 25, 3, '🍟', '#F6D27A'),
  (1, 4, 'Smoky Paneer Tikka', 'Charred paneer and vegetables in a spiced yogurt marinade.', '250 g paneer, cubed
1 capsicum, cubed
1 onion, in petals
1/2 cup thick curd
1 tbsp gram flour
1 tbsp ginger-garlic paste
1 tsp chilli powder
1 tsp garam masala
1 tbsp lemon juice
1 tbsp oil
Salt to taste', 'Mix curd, gram flour, spices, lemon juice and oil into a thick marinade.
Coat the paneer and vegetables and rest for 30 minutes.
Thread onto skewers.
Grill or roast at 220°C for 12–15 minutes, turning once.
Serve with mint chutney.', 20, 15, 3, '🍢', '#EF9F66'),
  (1, 5, 'Molten Chocolate Cake', 'A warm chocolate cake with a gooey centre that flows on the first cut.', '100 g dark chocolate
50 g butter
2 eggs
3 tbsp sugar
2 tbsp flour
A pinch of salt
Butter and cocoa for the moulds', 'Preheat the oven to 200°C. Butter two ramekins and dust with cocoa.
Melt the chocolate with the butter and cool slightly.
Whisk eggs and sugar until pale, then fold in the chocolate, flour and salt.
Divide into the moulds and bake 10–12 minutes, until the edges set and the centre wobbles.
Rest for a minute, invert onto a plate and serve warm.', 10, 12, 2, '🍫', '#C9A38A'),
  (1, 5, 'Gulab Jamun', 'Soft milk dumplings soaked in cardamom sugar syrup.', '1 cup milk powder
1/4 cup flour
1/4 tsp baking soda
2 tbsp ghee
About 1/4 cup warm milk
1 cup sugar
1 cup water
2 cardamom pods
Oil or ghee for frying', 'Simmer sugar, water and cardamom for 6 minutes to make a light syrup.
Mix milk powder, flour, baking soda and ghee, then add milk to form a soft dough.
Roll into smooth, crack-free balls.
Fry on low heat until deep golden brown.
Soak in warm syrup for at least 1 hour.', 20, 25, 6, '🍮', '#E7B07A'),
  (1, 5, 'Mango Panna Cotta', 'A wobbly vanilla-cream dessert crowned with fresh mango.', '1 cup cream
1/2 cup milk
3 tbsp sugar
1 tsp gelatin
2 tbsp water
1 cup mango puree
Mint to garnish', 'Bloom the gelatin in water for 5 minutes.
Warm the cream, milk and sugar until steaming; do not boil.
Stir in the gelatin until dissolved and pour into glasses.
Chill for 4 hours until set.
Top with mango puree and mint.', 15, 5, 4, '🥭', '#F8C35C'),
  (1, 6, 'Creamy Mango Lassi', 'Thick, chilled yogurt blended with ripe mango and a pinch of cardamom.', '1 cup ripe mango pulp
1 cup thick curd
1/2 cup cold milk
2 tbsp sugar
1 pinch cardamom powder
Ice cubes', 'Blend everything until smooth and frothy.
Taste and adjust the sweetness.
Pour over ice and serve cold.', 5, 0, 2, '🥛', '#F9CF6B'),
  (1, 6, 'Frothy Cold Coffee', 'Café-style iced coffee with vanilla ice cream and chocolate syrup.', '2 tsp instant coffee
2 tbsp sugar
1 cup cold milk
2 scoops vanilla ice cream
Ice cubes
Chocolate syrup', 'Dissolve the coffee and sugar in 2 tbsp warm water.
Blend with milk, ice cream and ice until thick.
Drizzle syrup inside the glass and pour in.', 5, 0, 2, '☕', '#CDAA8A'),
  (1, 6, 'Mint Lime Soda', 'A sparkling, sharp and cooling drink with muddled mint.', 'Juice of 2 limes
2 tbsp sugar syrup
1/4 tsp black salt
8 mint leaves
Chilled soda water
Ice', 'Muddle the mint with lime juice and black salt.
Add sugar syrup and ice.
Top with soda water and stir gently.', 5, 0, 2, '🍋', '#E5E58F');
