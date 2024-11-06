# Ticket Ordering System

This project is a ticket ordering system that allows users to create and manage ticket orders for events. It includes dynamic ticket pricing, ticket type selection, order processing, and database storage using PHP and MySQL.

## Features

- Dynamic form for selecting ticket types and quantities
- Real-time calculation of total ticket price
- Generation of unique barcodes for each ticket
- Backend processing for creating, approving, and saving orders
- Database storage with `orders` and `tickets` tables

## Requirements

- **PHP** (version 8.2 or above)
- **MySQL** (version 8.0 or above)
- **phpMyAdmin** (optional, for managing the database)
- **Web Server** (e.g., Apache, Nginx, or MAMP for local development)

## Project Structure

- **HTML/JavaScript**:
  - Form with dynamic fields for ticket quantity and types.
  - JavaScript functions to update ticket rows and calculate the total price.

- **PHP Backend**:
  - **Form Processing**: Handles form submission, calculates ticket prices, and generates unique barcodes.
  - **Database Interaction**: Saves order and ticket details in the database.
  - **Error Handling**: Catches errors during order creation, booking, and approval.

- **Database Tables**:
  - `orders`: Stores order information such as event ID, date, ticket quantities, and total price.
  - `tickets`: Stores individual ticket details linked to a specific order.
    
Table: `orders`

| order_id | event_id | event_date | ticket_adult_price | ticket_adult_quantity | ticket_kid_price | ticket_kid_quantity | user_id | equal_price | created             |
|----------|----------|------------|--------------------|-----------------------|------------------|---------------------|---------|-------------|---------------------|
| 1        | 1001     | 2024-11-15 | 50                | 2                     | 30               | 3                   | 1       | 190         | 2024-11-06 12:05:00 |
| 2        | 1002     | 2024-11-16 | 45                | 1                     | 25               | 2                   | 2       | 95          | 2024-11-06 12:10:00 |


### Table: `tickets`

| ticket_id | ticket_price | ticket_type      | ticker_barcode | order_id |
|-----------|--------------|------------------|----------------|----------|
| 1         | 50           | Adult            | 123456789      | 1        |
| 2         | 30           | Kid              | 987654321      | 1        |
| 3         | 30           | Kid              | 123123123      | 1        |
| 4         | 45           | Adult            | 456456456      | 2        |
| 5         | 25           | Kid              | 789789789      | 2        |

It was decided to remove barcode from "orders" table and create a second table ("tickets") for saving information for each ticket, so that each ticket could have it's own individual price, barcode and type.



## Usage

1. **Form Setup**:
   - Users can input event details, ticket prices, and quantities.
   - As quantities change, ticket rows are dynamically created, and the total price is updated in real-time.

2. **Order Submission**:
   - Upon form submission, the backend generates unique barcodes, processes the order, and stores it in the database.
   - A success message and generated barcodes are displayed upon successful order completion.

3. **Database Operations**:
   - Each order is stored in the `orders` table.
   - Tickets associated with the order are stored in the `tickets` table, linked by `order_id`.

### Creating an Order

The `createOrder` function in the PHP backend handles the submission and creation of an order. This function is invoked when the form is submitted and processes ticket types, quantities, and prices.

### Displaying Tickets and Orders

The frontend dynamically creates ticket rows for each ticket type (adult or kid), which are then stored in the database when the form is submitted.
