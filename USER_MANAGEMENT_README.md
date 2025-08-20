# PowerCollect User Management Setup

## Overview
This setup provides a multi-user admin panel system for the PowerCollect application with role-based access control.

## User Roles

### 1. Administrator (admin)
- Full access to all features
- Can create, edit, and delete users
- Access to all customer usage data
- Can manage customers and equipment

### 2. Manager (manager) 
- Can create and edit users (but not delete)
- Access to all customer usage data
- Can manage customers and equipment

### 3. User (user)
- Can add new customer usage records
- Can view and edit their own records
- Limited access to customer and equipment data

## Panel Access

### Admin Panel (`/admin`)
- Used by administrators and managers
- Full administrative features
- User management capabilities

### User Panel (`/user`)
- Used by regular users for data entry
- Simplified interface focused on adding usage records
- Customer usage management

## Default Login Credentials

| Role | Email | Password |
|------|-------|----------|
| Administrator | admin@powercollect.com | password |
| Manager | manager@powercollect.com | password |
| User | user@powercollect.com | password |

⚠️ **Important**: Change these default passwords immediately in production!

## Features

### User Management (Admin/Manager only)
- Create new user accounts
- Assign roles (admin, manager, user)
- Edit user information
- Password management

### Customer Usage Recording
- Add usage records with customer and equipment selection
- Date and time tracking
- kVA measurement recording
- Searchable and filterable data tables

### Data Access Control
- Role-based navigation and features
- Secure access to sensitive data
- Audit trail through timestamps

## Getting Started

1. **Access the Admin Panel**: Navigate to `/admin` and login with admin credentials
2. **Create User Accounts**: Use the User Management section to create accounts for your team
3. **Add Customer Usage**: Users can access `/user` to add customer usage records
4. **Manage Data**: Administrators can view all data through the admin panel

## Security Considerations

- All passwords are hashed using Laravel's secure hashing
- Role-based access control prevents unauthorized access
- Session management through Laravel's built-in authentication
- CSRF protection on all forms

## Database Structure

The system adds the following to your existing database:
- `role` column to the `users` table (admin, manager, user)
- `date` column to the `customer_usages` table for better tracking
