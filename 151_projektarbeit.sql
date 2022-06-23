-- Create Database
CREATE DATABASE 151_projektarbeit;
USE 151_projektarbeit;

-- Create & configure table
CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `firstname` varchar(30) NOT NULL,
  `lastname` varchar(30) NOT NULL,
  `username` varchar(30) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `admin` tinyint(1) NOT NULL,
  `creator` VARCHAR(30) NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `users` (`id`, `image`, `firstname`, `lastname`, `username`, `password`, `email`, `Admin`)
VALUES
(1, '', 'Lukas', 'Breiter', 'BreiterL', '$2y$10$EiEKtLTsLyubtAfp1ro0H.L6uNzCXokA9P7dQjytKvgA.U9Z25z/m', 'lukasbreiter@bluewin.ch', 1);

ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;COMMIT;

--create user for the project
CREATE USER 'DbUser'@'localhost' IDENTIFIED BY 'password1234';
GRANT SELECT, INSERT, UPDATE, DELETE ON `151_projektarbeit`.* TO 'DbUser'@'localhost';
