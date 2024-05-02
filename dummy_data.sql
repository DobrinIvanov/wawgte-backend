-- Inserting dummy data into the users table
INSERT INTO users (username, password, email, profile_picture) VALUES
('john_doe', 'password123', 'john@example.com', 'profile_pic1.jpg'),
('jane_smith', 'securepass', 'jane@example.com', 'profile_pic2.jpg'),
('sam_jackson', 'sam123', 'sam@example.com', NULL),
('emily_white', 'emilypass', 'emily@example.com', 'profile_pic3.jpg');

-- Inserting dummy data into the recipes table
INSERT INTO recipes (user_id, recipe_name, recipe_description, visibility) VALUES
(1, 'Pasta Carbonara', 'Classic pasta dish made with eggs, cheese, bacon, and black pepper.', 1),
(1, 'Chicken Alfredo', 'Creamy pasta dish with grilled chicken and Alfredo sauce.', 1),
(2, 'Chocolate Cake', 'Decadent chocolate cake with rich chocolate frosting.', 1),
(3, 'Greek Salad', 'Fresh salad with tomatoes, cucumbers, olives, and feta cheese.', 1);

-- Inserting dummy data into the recipe_books table
INSERT INTO recipe_books (user_id, visibility) VALUES
(1, 1),
(2, 1),
(3, 1),
(4, 1);

-- Inserting dummy data into the user_favorites table
INSERT INTO user_favorites (user_id, recipe_id) VALUES
(1, 1),
(1, 2),
(2, 3),
(3, 4);
