-- Insert sample client data
INSERT INTO `clients` (`name`, `email`, `phone`, `address`, `city`, `status`) VALUES
('John Banda', 'john.banda@email.com', '+265 888 123 456', 'Area 3, Lilongwe', 'Lilongwe', 1),
('Mary Phiri', 'mary.phiri@email.com', '+265 999 234 567', 'Ndirande, Blantyre', 'Blantyre', 1),
('Peter Moyo', 'peter.moyo@email.com', '+265 777 345 678', 'Zomba Road, Zomba', 'Zomba', 1),
('Grace Jere', 'grace.jere@email.com', '+265 888 456 789', 'Mangochi Boma', 'Mangochi', 1);

-- Insert sample animal data
INSERT INTO `animals` (`client_id`, `name`, `species`, `breed`, `gender`, `birth_date`, `color`, `weight`, `status`) VALUES
(1, 'Max', 'Dog', 'German Shepherd', 'male', '2020-05-15', 'Black and Tan', 28.50, 1),
(1, 'Bella', 'Dog', 'Local Breed', 'female', '2021-02-20', 'Brown', 15.20, 1),
(2, 'Simba', 'Cat', 'Domestic Shorthair', 'male', '2019-11-10', 'Orange Tabby', 4.80, 1),
(3, 'Rex', 'Dog', 'Rottweiler', 'male', '2018-08-25', 'Black and Brown', 35.00, 1),
(4, 'Mittens', 'Cat', 'Siamese', 'female', '2022-01-30', 'Cream Point', 3.50, 1);

-- Insert sample veterinary user
INSERT INTO `users` (`name`, `email`, `password`, `role`, `phone`, `status`) VALUES
('Dr. James Khumalo', 'dr.khumalo@vetlab.mw', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'veterinary', '+265 888 765 432', 1);

-- Insert sample treatment data
INSERT INTO `treatments` (`animal_id`, `veterinary_id`, `diagnosis`, `treatment_details`, `medication_prescribed`, `treatment_date`, `status`, `cost`) VALUES
(1, 2, 'Skin infection', 'Antibiotic course for skin infection, cleaning and dressing applied', 'Amoxicillin 250mg twice daily for 7 days', '2023-10-15', 'completed', 8500.00),
(3, 2, 'Routine checkup', 'Annual health check, vaccination update', 'Rabies vaccine administered', '2023-11-05', 'completed', 5000.00),
(2, 2, 'Flea infestation', 'Flea treatment applied, preventive measures discussed', 'Ivermectin injection, flea collar recommended', '2023-12-10', 'completed', 6500.00);

-- Insert sample vaccine data
INSERT INTO `vaccines` (`animal_id`, `veterinary_id`, `vaccine_name`, `vaccine_date`, `next_due_date`, `status`, `cost`) VALUES
(1, 2, 'Rabies Vaccine', '2023-10-15', '2024-10-15', 'administered', 2500.00),
(3, 2, 'Rabies Vaccine', '2023-11-05', '2024-11-05', 'administered', 2500.00),
(4, 2, 'FVRCP Vaccine', '2023-12-01', '2024-12-01', 'administered', 3000.00);

-- Insert sample billing data
INSERT INTO `billings` (`animal_id`, `treatment_id`, `vaccine_id`, `service_type`, `description`, `amount`, `tax_amount`, `total_amount`, `payment_method`, `payment_status`, `billing_date`) VALUES
(1, 1, 1, 'Consultation and Treatment', 'Skin infection treatment and rabies vaccination', 11000.00, 1815.00, 12815.00, 'cash', 'paid', '2023-10-15'),
(3, 2, 2, 'Annual Checkup', 'Routine health check and vaccination', 7500.00, 1237.50, 8737.50, 'mobile_money', 'paid', '2023-11-05'),
(2, 3, NULL, 'Flea Treatment', 'Flea infestation treatment', 6500.00, 1072.50, 7572.50, 'cash', 'pending', '2023-12-10');

-- Insert sample reminder data
INSERT INTO `reminders` (`animal_id`, `veterinary_id`, `type`, `title`, `message`, `reminder_date`, `status`, `send_sms`, `send_email`) VALUES
(1, 2, 'vaccination', 'Rabies Vaccine Due', 'Max is due for his annual rabies vaccination on 2024-10-15', '2024-10-08', 'pending', 1, 1),
(3, 2, 'vaccination', 'Rabies Vaccine Due', 'Simba is due for his annual rabies vaccination on 2024-11-05', '2024-10-29', 'pending', 1, 1),
(4, 2, 'checkup', 'Annual Checkup', 'Mittens is due for her annual health checkup', '2024-01-15', 'pending', 0, 1);

COMMIT;