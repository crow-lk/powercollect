# LECO React Frontend Setup Guide

## Overview
This guide provides the complete setup for creating a branded React frontend for the LECO Power Collection system.

## Prerequisites
1. Laravel backend running on `http://localhost:8000`
2. Node.js and npm installed
3. React project created with TypeScript support

## Project Structure
```
leco-frontend/
├── src/
│   ├── components/
│   │   ├── Auth/
│   │   │   ├── Login.tsx
│   │   │   └── PrivateRoute.tsx
│   │   ├── Layout/
│   │   │   ├── Header.tsx
│   │   │   ├── Sidebar.tsx
│   │   │   └── Layout.tsx
│   │   ├── Dashboard/
│   │   │   ├── Dashboard.tsx
│   │   │   └── StatsCard.tsx
│   │   ├── CustomerUsage/
│   │   │   ├── UsageForm.tsx
│   │   │   ├── UsageList.tsx
│   │   │   └── UsageFilters.tsx
│   │   └── Common/
│   │       ├── LoadingSpinner.tsx
│   │       └── ErrorMessage.tsx
│   ├── services/
│   │   └── api.ts
│   ├── contexts/
│   │   └── AuthContext.tsx
│   ├── hooks/
│   │   └── useAuth.ts
│   ├── theme/
│   │   └── lecoTheme.ts
│   ├── App.tsx
│   └── index.tsx
```

## Installation Commands

### 1. Create React App
```bash
cd /Users/kaviya/Documents/LECO
npx create-react-app leco-frontend --template typescript
cd leco-frontend
```

### 2. Install Dependencies
```bash
npm install axios @types/axios react-router-dom @types/react-router-dom @mui/material @emotion/react @emotion/styled @mui/icons-material
```

### 3. Install Additional UI Components
```bash
npm install @mui/x-date-pickers @mui/lab dayjs
```

## Core Files to Create

### 1. API Service (`src/services/api.ts`)
```typescript
import axios, { AxiosResponse } from 'axios';

const API_BASE_URL = 'http://localhost:8000/api';

const api = axios.create({
  baseURL: API_BASE_URL,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
});

// Request interceptor to add auth token
api.interceptors.request.use(
  (config) => {
    const token = localStorage.getItem('token');
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
  },
  (error) => {
    return Promise.reject(error);
  }
);

// Response interceptor to handle auth errors
api.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response?.status === 401) {
      localStorage.removeItem('token');
      localStorage.removeItem('user');
      window.location.href = '/login';
    }
    return Promise.reject(error);
  }
);

// Types
export interface User {
  id: number;
  name: string;
  email: string;
  role: string;
}

export interface Customer {
  id: number;
  name: string;
  account_no: string;
  address: string;
  nic?: string;
}

export interface Equipment {
  id: number;
  type: string;
  brand: string;
  model: string;
}

export interface CustomerUsage {
  id: number;
  customer_id: number;
  equipment_id: number;
  kVA: number;
  start_time: string;
  end_time: string;
  date: string;
  customer?: Customer;
  equipment?: Equipment;
  created_at?: string;
}

// API functions would go here...
export default api;
```

### 2. LECO Theme (`src/theme/lecoTheme.ts`)
```typescript
import { createTheme } from '@mui/material/styles';

const lecoTheme = createTheme({
  palette: {
    primary: {
      main: '#1976d2', // LECO Blue
      light: '#42a5f5',
      dark: '#1565c0',
      contrastText: '#ffffff',
    },
    secondary: {
      main: '#ff9800', // LECO Orange
      light: '#ffb74d',
      dark: '#f57c00',
      contrastText: '#ffffff',
    },
    background: {
      default: '#f5f5f5',
      paper: '#ffffff',
    },
    success: {
      main: '#4caf50',
    },
    warning: {
      main: '#ff9800',
    },
    error: {
      main: '#f44336',
    },
  },
  typography: {
    fontFamily: '"Roboto", "Helvetica", "Arial", sans-serif',
    h1: {
      fontSize: '2.5rem',
      fontWeight: 500,
      color: '#1976d2',
    },
    h2: {
      fontSize: '2rem',
      fontWeight: 500,
      color: '#1976d2',
    },
    h3: {
      fontSize: '1.75rem',
      fontWeight: 500,
    },
    h4: {
      fontSize: '1.5rem',
      fontWeight: 500,
    },
    h5: {
      fontSize: '1.25rem',
      fontWeight: 500,
    },
    h6: {
      fontSize: '1rem',
      fontWeight: 500,
    },
  },
  components: {
    MuiAppBar: {
      styleOverrides: {
        root: {
          backgroundColor: '#1976d2',
        },
      },
    },
    MuiButton: {
      styleOverrides: {
        root: {
          textTransform: 'none',
          borderRadius: 8,
        },
      },
    },
    MuiCard: {
      styleOverrides: {
        root: {
          borderRadius: 12,
          boxShadow: '0 2px 8px rgba(0,0,0,0.1)',
        },
      },
    },
    MuiTextField: {
      styleOverrides: {
        root: {
          '& .MuiOutlinedInput-root': {
            borderRadius: 8,
          },
        },
      },
    },
  },
});

export default lecoTheme;
```

### 3. Auth Context (`src/contexts/AuthContext.tsx`)
```typescript
import React, { createContext, useContext, useState, useEffect, ReactNode } from 'react';
import { User, authAPI, LoginCredentials } from '../services/api';

interface AuthContextType {
  user: User | null;
  loading: boolean;
  login: (credentials: LoginCredentials) => Promise<void>;
  logout: () => void;
  isAuthenticated: boolean;
}

const AuthContext = createContext<AuthContextType | undefined>(undefined);

export const useAuth = () => {
  const context = useContext(AuthContext);
  if (context === undefined) {
    throw new Error('useAuth must be used within an AuthProvider');
  }
  return context;
};

interface AuthProviderProps {
  children: ReactNode;
}

export const AuthProvider: React.FC<AuthProviderProps> = ({ children }) => {
  const [user, setUser] = useState<User | null>(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const token = localStorage.getItem('token');
    const storedUser = localStorage.getItem('user');
    
    if (token && storedUser) {
      setUser(JSON.parse(storedUser));
    }
    setLoading(false);
  }, []);

  const login = async (credentials: LoginCredentials) => {
    try {
      const response = await authAPI.login(credentials);
      localStorage.setItem('token', response.token);
      localStorage.setItem('user', JSON.stringify(response.user));
      setUser(response.user);
    } catch (error) {
      throw error;
    }
  };

  const logout = () => {
    localStorage.removeItem('token');
    localStorage.removeItem('user');
    setUser(null);
    authAPI.logout().catch(() => {
      // Handle logout error silently
    });
  };

  const value = {
    user,
    loading,
    login,
    logout,
    isAuthenticated: !!user,
  };

  return <AuthContext.Provider value={value}>{children}</AuthContext.Provider>;
};
```

### 4. Main App Component (`src/App.tsx`)
```typescript
import React from 'react';
import { BrowserRouter as Router, Routes, Route, Navigate } from 'react-router-dom';
import { ThemeProvider } from '@mui/material/styles';
import { CssBaseline } from '@mui/material';
import { AuthProvider } from './contexts/AuthContext';
import lecoTheme from './theme/lecoTheme';
import Login from './components/Auth/Login';
import Dashboard from './components/Dashboard/Dashboard';
import UsageForm from './components/CustomerUsage/UsageForm';
import UsageList from './components/CustomerUsage/UsageList';
import Layout from './components/Layout/Layout';
import PrivateRoute from './components/Auth/PrivateRoute';

function App() {
  return (
    <ThemeProvider theme={lecoTheme}>
      <CssBaseline />
      <AuthProvider>
        <Router>
          <Routes>
            <Route path="/login" element={<Login />} />
            <Route
              path="/"
              element={
                <PrivateRoute>
                  <Layout />
                </PrivateRoute>
              }
            >
              <Route index element={<Navigate to="/dashboard" replace />} />
              <Route path="dashboard" element={<Dashboard />} />
              <Route path="usage/new" element={<UsageForm />} />
              <Route path="usage/list" element={<UsageList />} />
            </Route>
          </Routes>
        </Router>
      </AuthProvider>
    </ThemeProvider>
  );
}

export default App;
```

## Key Features

### 1. LECO Branding
- Custom color scheme using LECO corporate colors
- Professional typography and spacing
- Branded header with LECO logo
- Consistent design language

### 2. User Authentication
- JWT token-based authentication
- Automatic token refresh
- Role-based access control
- Secure logout functionality

### 3. Data Entry Forms
- Customer usage recording form
- Real-time validation
- Autocomplete for customers and equipment
- Date and time pickers

### 4. Data Management
- Paginated usage records list
- Advanced filtering options
- Search functionality
- Export capabilities

### 5. Dashboard
- Usage statistics
- Recent activities
- Quick actions
- Charts and graphs

## Development Commands

### Start Development Server
```bash
npm start
```

### Build for Production
```bash
npm run build
```

### Run Tests
```bash
npm test
```

## LECO Specific Customizations

### 1. Header Branding
- LECO logo and company name
- Corporate color scheme
- Navigation specific to power collection

### 2. Form Labels and Terminology
- Use power industry terminology
- Local Sri Lankan context
- LECO-specific field names

### 3. Data Validation
- Sri Lankan NIC validation
- Power consumption ranges
- Time format validation

### 4. Reporting Features
- Power usage reports
- Customer billing summaries
- Equipment utilization reports

## Integration with Laravel Backend

### API Endpoints Used:
- `POST /api/login` - User authentication
- `GET /api/customers` - Fetch customers
- `GET /api/equipment` - Fetch equipment
- `POST /api/customer-usage` - Create usage record
- `GET /api/customer-usage` - Fetch usage records
- `GET /api/statistics` - Dashboard statistics

### Authentication Flow:
1. User logs in via React form
2. Laravel validates credentials
3. Laravel returns JWT token
4. React stores token in localStorage
5. All subsequent API calls include token
6. Laravel validates token on each request

This React frontend provides a modern, branded interface for LECO's power collection system while maintaining seamless integration with the Laravel backend.
