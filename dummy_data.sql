-- Inserting dummy data into users table
INSERT INTO users (username, email, password) VALUES
('user1', 'user1@example.com', 'password1'),
('user2', 'user2@example.com', 'password2'),
('user3', 'user3@example.com', 'password3');

-- Inserting dummy data into recipes table
INSERT INTO recipes (title, description, instructions, user_id) VALUES
('Recipe 1', 'Description for Recipe 1', 'Instructions for Recipe 1', 1),
('Recipe 2', 'Description for Recipe 2', 'Instructions for Recipe 2', 2),
('Recipe 3', 'Description for Recipe 3', 'Instructions for Recipe 3', 3);

-- Inserting dummy data into cookbooks table
INSERT INTO cookbooks (title, description, user_id) VALUES
('Cookbook 1', 'Description for Cookbook 1', 1),
('Cookbook 2', 'Description for Cookbook 2', 2),
('Cookbook 3', 'Description for Cookbook 3', 3);

-- Inserting dummy data into favorites table
INSERT INTO favorites (user_id, recipe_id) VALUES
(1, 1),
(2, 2),
(3, 3);

-- Inserting dummy data into tags table
INSERT INTO tags (tag_name) VALUES
('Vegetarian'),
('Dessert'),
('Italian');

-- Inserting dummy data into recipe_tags table
INSERT INTO recipe_tags (recipe_id, tag_id) VALUES
(1, 1),
(1, 2),
(2, 2),
(2, 3),
(3, 1),
(3, 3);
