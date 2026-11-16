-- Recipe Sharing Portal | sample queries that show the DBMS concepts used
USE recipe_portal;

-- JOIN: recipes with their category and the user who shared them
SELECT r.title, c.name AS category, u.username
FROM recipes r
JOIN categories c ON c.category_id = r.category_id
JOIN users u      ON u.user_id = r.user_id
ORDER BY r.created_at DESC;

-- GROUP BY + aggregate: number of recipes per category
SELECT c.name, COUNT(r.recipe_id) AS total_recipes
FROM categories c
LEFT JOIN recipes r ON r.category_id = c.category_id
GROUP BY c.category_id, c.name;

-- Search: recipes whose title or ingredients contain a word
SELECT recipe_id, title
FROM recipes
WHERE title LIKE '%paneer%' OR ingredients LIKE '%paneer%';

-- Filter: quick recipes (total time 20 minutes or less)
SELECT title, prep_time + cook_time AS total_minutes
FROM recipes
WHERE prep_time + cook_time <= 20
ORDER BY total_minutes;

-- CRUD
-- INSERT INTO recipes (...) VALUES (...);
-- UPDATE recipes SET title = 'New title' WHERE recipe_id = 1;
-- DELETE FROM recipes WHERE recipe_id = 1;
