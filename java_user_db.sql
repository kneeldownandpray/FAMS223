-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 08, 2025 at 11:26 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `java_user_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `informationsoffamilies`
--

CREATE TABLE `informationsoffamilies` (
  `firstname` varchar(255) NOT NULL,
  `id` int(255) NOT NULL,
  `lastname` varchar(255) NOT NULL,
  `middlename` varchar(255) NOT NULL,
  `suffix` varchar(255) NOT NULL,
  `dateofbirth` varchar(255) NOT NULL,
  `gender` varchar(255) NOT NULL,
  `age` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL,
  `philhealth` varchar(255) NOT NULL,
  `familyplanning` varchar(255) NOT NULL,
  `comorvitidis` varchar(255) NOT NULL,
  `smoker` varchar(255) NOT NULL,
  `toilet` varchar(255) NOT NULL,
  `poso` varchar(255) NOT NULL,
  `remarks` varchar(255) NOT NULL,
  `compound` varchar(255) NOT NULL,
  `purok` varchar(255) NOT NULL,
  `familynumber` varchar(255) NOT NULL,
  `cellphonenumber` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `informationsoffamilies`
--

INSERT INTO `informationsoffamilies` (`firstname`, `id`, `lastname`, `middlename`, `suffix`, `dateofbirth`, `gender`, `age`, `status`, `philhealth`, `familyplanning`, `comorvitidis`, `smoker`, `toilet`, `poso`, `remarks`, `compound`, `purok`, `familynumber`, `cellphonenumber`) VALUES
('Juan', 7, 'Dela Cruz', 'Reyes', '', '1985-06-15', 'Male', '39', 'Married', '1', '0', '1', '0', '1', '0', 'No Remarks', 'A', '1', 'F001', '09123456789'),
('Pedro', 9, 'Garcia', 'Lopez', 'Jr.', '1978-11-05', 'Male', '46', 'Widowed', '1', '0', '1', '1', '0', '1', 'Asthma', 'C', '3', 'F003', '09345678901'),
('Anna', 10, 'Fernandez', 'Cruz', '', '1995-07-30', 'Female', '29', 'Married', '1', '1', '0', '0', '1', '0', 'No Remarks', 'D', '4', 'F004', '09456789012'),
('Luis', 11, 'Torres', 'Diaz', '', '2000-02-10', 'Male', '24', 'Single', '0', '1', '1', '0', '0', '1', 'Diabetes', 'E', '5', 'F005', '09567890123'),
('Carmen', 12, 'Ramos', 'Morales', '', '1982-09-18', 'Female', '42', 'Married', '1', '0', '0', '1', '1', '0', 'Hypertension', 'F', '6', 'F006', '09678901234'),
('Ricardo', 13, 'Vega', 'Domingo', '', '1972-05-21', 'Male', '52', 'Married', '1', '1', '0', '0', '0', '1', 'No Remarks', 'A', '1', 'F007', '09789012345'),
('Isabel', 14, 'Mendoza', 'Castro', '', '1998-12-14', 'Female', '26', 'Single', '0', '1', '1', '0', '1', '0', 'Anemia', 'B', '2', 'F008', '09890123456'),
('Carlos', 15, 'Gonzalez', 'Hernandez', 'Sr.', '1987-08-03', 'Male', '37', 'Married', '1', '0', '1', '1', '0', '1', 'No Remarks', 'C', '3', 'F009', '09901234567'),
('Sophia', 16, 'Reyes', 'Ortiz', '', '1993-04-25', 'Female', '31', 'Single', '0', '0', '0', '0', '0', '0', 'No Remarks', 'test', '4', 'F010', '09012345678'),
('Daniel', 17, 'Navarro', 'Flores', '', '1975-09-30', 'Male', '49', 'Married', '1', '1', '1', '1', '0', '0', 'No Remarks', 'E', '5', 'F011', '09123456780'),
('Miguel', 19, 'Padilla', 'Santos', '', '1997-11-19', 'Male', '27', 'Single', '1', '0', '0', '0', '0', '1', 'No Remarks', 'A', '1', 'F013', '09345678902'),
('Elena', 20, 'Gutierrez', 'Marquez', '', '2002-02-07', 'Female', '22', 'Single', '0', '1', '1', '0', '1', '0', 'No Remarks', 'B', '2', 'F014', '09456789013'),
('Javier', 21, 'Ortega', 'Lozano', '', '1965-07-23', 'Male', '59', 'Married', '1', '0', '1', '1', '0', '1', 'No Remarks', 'C', '3', 'F015', '09567890124'),
('Carla', 22, 'Romero', 'Cabrera', '', '1983-05-14', 'Female', '41', 'Married', '0', '0', '0', '0', '0', '0', 'No Remarks', 'D', '4', 'F016', '09678901235'),
('Roberto', 23, 'Fernandez', 'Mendez', '', '1979-10-29', 'Male', '45', 'Widowed', '0', '0', '1', '1', '0', '1', 'No Remarks', 'E', '5', 'F017', '09789012346'),
('Valeria', 24, 'Salazar', 'Pineda', '', '1991-03-09', 'Female', '33', 'Single', '1', '1', '0', '0', '1', '1', 'No Remarks', 'F', '6', 'F018', '09890123457'),
('Enrique', 25, 'Escobar', 'Benitez', '', '1980-12-31', 'Male', '44', 'Married', '0', '0', '1', '1', '0', '0', 'No Remarks', 'A', '1', 'F019', '09901234568'),
('Beatriz', 26, 'Nunez', 'Carrillo', '', '2001-06-05', 'Female', '23', 'Single', '1', '1', '0', '0', '1', '0', 'No Remarks', 'B', '2', 'F020', '09012345679'),
('Pedro', 27, 'Garcia', 'Lopez', 'Jr.', '1978-11-05', 'Male', '46', 'Widowed', '0', '0', '0', '0', '0', '0', 'Asthma', 'C', '3', 'F003', '09345678901'),
('Isabel', 28, 'Mendoza', 'Castro', '', '1998-12-14', 'Female', '26', 'Single', '0', '0', '0', '0', '0', '0', 'Anemia', 'B', '2', 'F008', '09890123456'),
('joseph Gabriel', 29, 'Dichoso', 'Metica', '', '1965-07-23', 'Male', '59', 'Married', '0', '0', '0', '0', '0', '0', 'No Remarks', 'C', '3', 'F01523', '09567890124'),
('Enriques', 30, 'Escobars', 'Benitezs', '', '1980-12-31', 'Male', '44', 'Married', '0', '0', '0', '0', '0', '0', 'No Remarks', 'A', '1', 'F019', '09901234568'),
('Roberto', 31, 'Fernandez', 'Mendez', '', '1979-10-29', 'Male', '45', 'Widowed', '0', '0', '0', '0', '0', '0', 'No Remarks', 'E', '5', 'F017', '09789012346'),
('Valeria', 32, 'Salazar', 'Pineda', '', '1991-03-09', 'Female', '33', 'Single', '0', '0', '0', '0', '0', '0', 'No Remarks', 'F', '6', 'F018', '09890123457'),
('Javier', 33, 'Ortega', 'Lozano', '', '1965-07-23', 'Male', '59', 'Married', '0', '0', '0', '0', '0', '0', 'No Remarks', 'C', '3', 'F015', '09567890124'),
('Elena', 34, 'Gutierrez', 'Marquez', '', '2002-02-07', 'Female', '22', 'Single', '0', '0', '0', '0', '0', '0', 'No Remarks', 'B', '2', 'F014', '09456789013');

-- --------------------------------------------------------

--
-- Table structure for table `peopleinfo`
--

CREATE TABLE `peopleinfo` (
  `id` int(25) NOT NULL,
  `Name` text NOT NULL,
  `compound` varchar(255) NOT NULL,
  `bday` varchar(255) NOT NULL,
  `gender` varchar(255) NOT NULL,
  `age` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL,
  `philhealth` varchar(255) NOT NULL,
  `familyplanning` varchar(255) NOT NULL,
  `comorviditis` varchar(255) NOT NULL,
  `smoker` varchar(255) NOT NULL,
  `toilet` varchar(255) NOT NULL,
  `gripoposo` varchar(255) NOT NULL,
  `remarks` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `relationshiptb`
--

CREATE TABLE `relationshiptb` (
  `id` int(255) NOT NULL,
  `memberid` int(255) NOT NULL,
  `relationshipid` int(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `relationshiptb`
--

INSERT INTO `relationshiptb` (`id`, `memberid`, `relationshipid`) VALUES
(1, 2, 3),
(2, 1, 4),
(3, 1, 5);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `full_name` varchar(127) NOT NULL,
  `email` varchar(127) NOT NULL,
  `password` varchar(127) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `full_name`, `email`, `password`) VALUES
(1, 'Jorelle Dichosod', 'jorelledichoso84@gmail.com', '108239120363'),
(2, 'miguel', 'miguel@miguel', 'miguel'),
(3, 'pia', 'pia', 'pia'),
(4, 'joseph', 'joseph@joseph', 'joseph'),
(5, 'Gabriel', 'Gabriel@Gabriel', 'Gabriel'),
(29, 'test', 'test@test', 'test@test');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `informationsoffamilies`
--
ALTER TABLE `informationsoffamilies`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `peopleinfo`
--
ALTER TABLE `peopleinfo`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `relationshiptb`
--
ALTER TABLE `relationshiptb`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `informationsoffamilies`
--
ALTER TABLE `informationsoffamilies`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `peopleinfo`
--
ALTER TABLE `peopleinfo`
  MODIFY `id` int(25) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `relationshiptb`
--
ALTER TABLE `relationshiptb`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
