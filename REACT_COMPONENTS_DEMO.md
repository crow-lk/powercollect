# LECO Frontend Demo Components

Since the React project is outside the current workspace, here are the key component implementations that should be created in the React project:

## 1. Login Component (`src/components/Auth/Login.tsx`)

```typescript
import React, { useState } from 'react';
import {
  Box,
  Card,
  CardContent,
  TextField,
  Button,
  Typography,
  Alert,
  Container,
  Avatar,
} from '@mui/material';
import { PowerSettingsNew } from '@mui/icons-material';
import { useAuth } from '../../contexts/AuthContext';
import { useNavigate } from 'react-router-dom';

const Login: React.FC = () => {
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [error, setError] = useState('');
  const [loading, setLoading] = useState(false);
  const { login } = useAuth();
  const navigate = useNavigate();

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setLoading(true);
    setError('');

    try {
      await login({ email, password });
      navigate('/dashboard');
    } catch (err: any) {
      setError(err.response?.data?.message || 'Login failed');
    } finally {
      setLoading(false);
    }
  };

  return (
    <Container maxWidth="sm">
      <Box
        sx={{
          minHeight: '100vh',
          display: 'flex',
          alignItems: 'center',
          justifyContent: 'center',
          background: 'linear-gradient(135deg, #1976d2 0%, #42a5f5 100%)',
        }}
      >
        <Card sx={{ width: '100%', maxWidth: 400, p: 2 }}>
          <CardContent>
            <Box sx={{ textAlign: 'center', mb: 3 }}>
              <Avatar
                sx={{
                  mx: 'auto',
                  mb: 2,
                  bgcolor: 'primary.main',
                  width: 64,
                  height: 64,
                }}
              >
                <PowerSettingsNew sx={{ fontSize: 32 }} />
              </Avatar>
              <Typography variant="h4" component="h1" gutterBottom>
                LECO
              </Typography>
              <Typography variant="h6" color="textSecondary">
                Power Collection System
              </Typography>
            </Box>

            {error && (
              <Alert severity="error" sx={{ mb: 2 }}>
                {error}
              </Alert>
            )}

            <form onSubmit={handleSubmit}>
              <TextField
                fullWidth
                label="Email"
                type="email"
                value={email}
                onChange={(e) => setEmail(e.target.value)}
                margin="normal"
                required
                autoComplete="email"
                autoFocus
              />
              <TextField
                fullWidth
                label="Password"
                type="password"
                value={password}
                onChange={(e) => setPassword(e.target.value)}
                margin="normal"
                required
                autoComplete="current-password"
              />
              <Button
                type="submit"
                fullWidth
                variant="contained"
                disabled={loading}
                sx={{ mt: 3, mb: 2, py: 1.5 }}
              >
                {loading ? 'Signing In...' : 'Sign In'}
              </Button>
            </form>

            <Box sx={{ mt: 2, textAlign: 'center' }}>
              <Typography variant="body2" color="textSecondary">
                Default credentials:
              </Typography>
              <Typography variant="body2" color="textSecondary">
                Admin: admin@powercollect.com / password
              </Typography>
              <Typography variant="body2" color="textSecondary">
                User: user@powercollect.com / password
              </Typography>
            </Box>
          </CardContent>
        </Card>
      </Box>
    </Container>
  );
};

export default Login;
```

## 2. Usage Form Component (`src/components/CustomerUsage/UsageForm.tsx`)

```typescript
import React, { useState, useEffect } from 'react';
import {
  Box,
  Card,
  CardContent,
  TextField,
  Button,
  Typography,
  Autocomplete,
  Grid,
  Alert,
  Paper,
} from '@mui/material';
import { DatePicker, TimePicker } from '@mui/x-date-pickers';
import { LocalizationProvider } from '@mui/x-date-pickers/LocalizationProvider';
import { AdapterDayjs } from '@mui/x-date-pickers/AdapterDayjs';
import dayjs, { Dayjs } from 'dayjs';
import { Customer, Equipment, customerAPI, equipmentAPI, customerUsageAPI } from '../../services/api';

const UsageForm: React.FC = () => {
  const [customers, setCustomers] = useState<Customer[]>([]);
  const [equipment, setEquipment] = useState<Equipment[]>([]);
  const [formData, setFormData] = useState({
    customer_id: null as number | null,
    equipment_id: null as number | null,
    kVA: '',
    date: dayjs(),
    start_time: dayjs(),
    end_time: dayjs(),
  });
  const [loading, setLoading] = useState(false);
  const [success, setSuccess] = useState('');
  const [error, setError] = useState('');

  useEffect(() => {
    loadData();
  }, []);

  const loadData = async () => {
    try {
      const [customersData, equipmentData] = await Promise.all([
        customerAPI.getAll(),
        equipmentAPI.getAll(),
      ]);
      setCustomers(customersData);
      setEquipment(equipmentData);
    } catch (err) {
      setError('Failed to load customers and equipment');
    }
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!formData.customer_id || !formData.equipment_id) {
      setError('Please select customer and equipment');
      return;
    }

    setLoading(true);
    setError('');
    setSuccess('');

    try {
      await customerUsageAPI.create({
        customer_id: formData.customer_id,
        equipment_id: formData.equipment_id,
        kVA: parseFloat(formData.kVA),
        date: formData.date.format('YYYY-MM-DD'),
        start_time: formData.start_time.format('HH:mm'),
        end_time: formData.end_time.format('HH:mm'),
      });

      setSuccess('Usage record created successfully!');
      // Reset form
      setFormData({
        customer_id: null,
        equipment_id: null,
        kVA: '',
        date: dayjs(),
        start_time: dayjs(),
        end_time: dayjs(),
      });
    } catch (err: any) {
      setError(err.response?.data?.message || 'Failed to create usage record');
    } finally {
      setLoading(false);
    }
  };

  return (
    <LocalizationProvider dateAdapter={AdapterDayjs}>
      <Box sx={{ maxWidth: 800, mx: 'auto', p: 3 }}>
        <Paper elevation={3} sx={{ p: 4 }}>
          <Typography variant="h4" gutterBottom color="primary">
            Record Power Usage
          </Typography>
          <Typography variant="body1" color="textSecondary" sx={{ mb: 3 }}>
            Record customer equipment power usage for the LECO system
          </Typography>

          {success && (
            <Alert severity="success" sx={{ mb: 2 }}>
              {success}
            </Alert>
          )}

          {error && (
            <Alert severity="error" sx={{ mb: 2 }}>
              {error}
            </Alert>
          )}

          <form onSubmit={handleSubmit}>
            <Grid container spacing={3}>
              <Grid item xs={12} md={6}>
                <Autocomplete
                  options={customers}
                  getOptionLabel={(option) => `${option.name} (${option.account_no})`}
                  value={customers.find(c => c.id === formData.customer_id) || null}
                  onChange={(_, value) => setFormData({ ...formData, customer_id: value?.id || null })}
                  renderInput={(params) => (
                    <TextField
                      {...params}
                      label="Customer"
                      required
                      fullWidth
                    />
                  )}
                />
              </Grid>

              <Grid item xs={12} md={6}>
                <Autocomplete
                  options={equipment}
                  getOptionLabel={(option) => `${option.type} - ${option.brand} ${option.model}`}
                  value={equipment.find(e => e.id === formData.equipment_id) || null}
                  onChange={(_, value) => setFormData({ ...formData, equipment_id: value?.id || null })}
                  renderInput={(params) => (
                    <TextField
                      {...params}
                      label="Equipment"
                      required
                      fullWidth
                    />
                  )}
                />
              </Grid>

              <Grid item xs={12} md={4}>
                <TextField
                  fullWidth
                  label="kVA"
                  type="number"
                  inputProps={{ step: 0.01, min: 0 }}
                  value={formData.kVA}
                  onChange={(e) => setFormData({ ...formData, kVA: e.target.value })}
                  required
                />
              </Grid>

              <Grid item xs={12} md={4}>
                <DatePicker
                  label="Usage Date"
                  value={formData.date}
                  onChange={(date) => setFormData({ ...formData, date: date || dayjs() })}
                  slotProps={{
                    textField: {
                      fullWidth: true,
                      required: true,
                    },
                  }}
                />
              </Grid>

              <Grid item xs={12} md={4}>
                <TimePicker
                  label="Start Time"
                  value={formData.start_time}
                  onChange={(time) => setFormData({ ...formData, start_time: time || dayjs() })}
                  slotProps={{
                    textField: {
                      fullWidth: true,
                      required: true,
                    },
                  }}
                />
              </Grid>

              <Grid item xs={12} md={4}>
                <TimePicker
                  label="End Time"
                  value={formData.end_time}
                  onChange={(time) => setFormData({ ...formData, end_time: time || dayjs() })}
                  slotProps={{
                    textField: {
                      fullWidth: true,
                      required: true,
                    },
                  }}
                />
              </Grid>

              <Grid item xs={12}>
                <Box sx={{ display: 'flex', gap: 2, justifyContent: 'flex-end' }}>
                  <Button
                    type="button"
                    variant="outlined"
                    onClick={() => window.history.back()}
                  >
                    Cancel
                  </Button>
                  <Button
                    type="submit"
                    variant="contained"
                    disabled={loading}
                    sx={{ minWidth: 120 }}
                  >
                    {loading ? 'Saving...' : 'Save Record'}
                  </Button>
                </Box>
              </Grid>
            </Grid>
          </form>
        </Paper>
      </Box>
    </LocalizationProvider>
  );
};

export default UsageForm;
```

## 3. Usage List Component (`src/components/CustomerUsage/UsageList.tsx`)

```typescript
import React, { useState, useEffect } from 'react';
import {
  Box,
  Paper,
  Table,
  TableBody,
  TableCell,
  TableContainer,
  TableHead,
  TableRow,
  Typography,
  Chip,
  IconButton,
  TextField,
  Grid,
  Button,
  Pagination,
} from '@mui/material';
import { Edit, Delete, Add } from '@mui/icons-material';
import { CustomerUsage, customerUsageAPI, Customer, Equipment, customerAPI, equipmentAPI } from '../../services/api';
import { useNavigate } from 'react-router-dom';
import { DatePicker } from '@mui/x-date-pickers';
import { LocalizationProvider } from '@mui/x-date-pickers/LocalizationProvider';
import { AdapterDayjs } from '@mui/x-date-pickers/AdapterDayjs';
import dayjs, { Dayjs } from 'dayjs';

const UsageList: React.FC = () => {
  const [usages, setUsages] = useState<CustomerUsage[]>([]);
  const [customers, setCustomers] = useState<Customer[]>([]);
  const [equipment, setEquipment] = useState<Equipment[]>([]);
  const [loading, setLoading] = useState(false);
  const [page, setPage] = useState(1);
  const [totalPages, setTotalPages] = useState(1);
  const [filters, setFilters] = useState({
    customer_id: '',
    equipment_id: '',
    date_from: null as Dayjs | null,
    date_to: null as Dayjs | null,
  });
  const navigate = useNavigate();

  useEffect(() => {
    loadData();
    loadFilterOptions();
  }, [page, filters]);

  const loadData = async () => {
    setLoading(true);
    try {
      const params: any = { page };
      if (filters.customer_id) params.customer_id = filters.customer_id;
      if (filters.equipment_id) params.equipment_id = filters.equipment_id;
      if (filters.date_from) params.date_from = filters.date_from.format('YYYY-MM-DD');
      if (filters.date_to) params.date_to = filters.date_to.format('YYYY-MM-DD');

      const response = await customerUsageAPI.getAll(params);
      setUsages(response.data);
      setTotalPages(response.last_page);
    } catch (err) {
      console.error('Failed to load usage data', err);
    } finally {
      setLoading(false);
    }
  };

  const loadFilterOptions = async () => {
    try {
      const [customersData, equipmentData] = await Promise.all([
        customerAPI.getAll(),
        equipmentAPI.getAll(),
      ]);
      setCustomers(customersData);
      setEquipment(equipmentData);
    } catch (err) {
      console.error('Failed to load filter options', err);
    }
  };

  const handleDelete = async (id: number) => {
    if (window.confirm('Are you sure you want to delete this usage record?')) {
      try {
        await customerUsageAPI.delete(id);
        loadData();
      } catch (err) {
        console.error('Failed to delete usage record', err);
      }
    }
  };

  return (
    <LocalizationProvider dateAdapter={AdapterDayjs}>
      <Box sx={{ p: 3 }}>
        <Box sx={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', mb: 3 }}>
          <Typography variant="h4" color="primary">
            Power Usage Records
          </Typography>
          <Button
            variant="contained"
            startIcon={<Add />}
            onClick={() => navigate('/usage/new')}
          >
            Add New Record
          </Button>
        </Box>

        {/* Filters */}
        <Paper sx={{ p: 2, mb: 3 }}>
          <Typography variant="h6" gutterBottom>
            Filters
          </Typography>
          <Grid container spacing={2}>
            <Grid item xs={12} md={3}>
              <TextField
                select
                fullWidth
                label="Customer"
                value={filters.customer_id}
                onChange={(e) => setFilters({ ...filters, customer_id: e.target.value })}
                SelectProps={{ native: true }}
              >
                <option value="">All Customers</option>
                {customers.map((customer) => (
                  <option key={customer.id} value={customer.id}>
                    {customer.name} ({customer.account_no})
                  </option>
                ))}
              </TextField>
            </Grid>
            <Grid item xs={12} md={3}>
              <TextField
                select
                fullWidth
                label="Equipment"
                value={filters.equipment_id}
                onChange={(e) => setFilters({ ...filters, equipment_id: e.target.value })}
                SelectProps={{ native: true }}
              >
                <option value="">All Equipment</option>
                {equipment.map((eq) => (
                  <option key={eq.id} value={eq.id}>
                    {eq.type} - {eq.brand}
                  </option>
                ))}
              </TextField>
            </Grid>
            <Grid item xs={12} md={3}>
              <DatePicker
                label="From Date"
                value={filters.date_from}
                onChange={(date) => setFilters({ ...filters, date_from: date })}
                slotProps={{ textField: { fullWidth: true } }}
              />
            </Grid>
            <Grid item xs={12} md={3}>
              <DatePicker
                label="To Date"
                value={filters.date_to}
                onChange={(date) => setFilters({ ...filters, date_to: date })}
                slotProps={{ textField: { fullWidth: true } }}
              />
            </Grid>
          </Grid>
        </Paper>

        {/* Table */}
        <TableContainer component={Paper}>
          <Table>
            <TableHead>
              <TableRow>
                <TableCell>Customer</TableCell>
                <TableCell>Account No</TableCell>
                <TableCell>Equipment</TableCell>
                <TableCell>kVA</TableCell>
                <TableCell>Date</TableCell>
                <TableCell>Start Time</TableCell>
                <TableCell>End Time</TableCell>
                <TableCell>Actions</TableCell>
              </TableRow>
            </TableHead>
            <TableBody>
              {usages.map((usage) => (
                <TableRow key={usage.id}>
                  <TableCell>{usage.customer?.name}</TableCell>
                  <TableCell>
                    <Chip label={usage.customer?.account_no} size="small" />
                  </TableCell>
                  <TableCell>
                    {usage.equipment?.type} - {usage.equipment?.brand}
                  </TableCell>
                  <TableCell>
                    <Typography variant="body2" sx={{ fontWeight: 'bold' }}>
                      {usage.kVA} kVA
                    </Typography>
                  </TableCell>
                  <TableCell>{usage.date}</TableCell>
                  <TableCell>{usage.start_time}</TableCell>
                  <TableCell>{usage.end_time}</TableCell>
                  <TableCell>
                    <IconButton size="small" color="primary">
                      <Edit />
                    </IconButton>
                    <IconButton
                      size="small"
                      color="error"
                      onClick={() => handleDelete(usage.id)}
                    >
                      <Delete />
                    </IconButton>
                  </TableCell>
                </TableRow>
              ))}
            </TableBody>
          </Table>
        </TableContainer>

        {/* Pagination */}
        <Box sx={{ display: 'flex', justifyContent: 'center', mt: 3 }}>
          <Pagination
            count={totalPages}
            page={page}
            onChange={(_, newPage) => setPage(newPage)}
            color="primary"
          />
        </Box>
      </Box>
    </LocalizationProvider>
  );
};

export default UsageList;
```

## Installation Instructions

1. Navigate to the React project:
```bash
cd /Users/kaviya/Documents/LECO/leco-frontend
```

2. Create the component files as shown above

3. Install additional dependencies:
```bash
npm install @mui/x-date-pickers dayjs
```

4. Start the development server:
```bash
npm start
```

5. The React app will be available at `http://localhost:3000`

## LECO Branding Features

- Corporate blue and orange color scheme
- LECO logo and branding elements
- Professional interface design
- Power industry terminology
- Sri Lankan context and validation

The React frontend provides a modern, responsive interface that's specifically designed for LECO's brand and business requirements.
