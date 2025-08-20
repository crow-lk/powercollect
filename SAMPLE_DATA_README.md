# PowerCollect Sample Data - Galle, Sri Lanka

## Overview
This document describes the sample data created for the PowerCollect system, specifically designed for power usage measurement in Galle, Sri Lanka.

## Sample Data Summary

### Customers (8 total)
The sample includes a mix of residential, commercial, and industrial customers from the Galle area:

1. **K.M. Perera** (LECO-GL-001) - Residential customer in Galle Fort
2. **S.A. Fernando** (LECO-GL-002) - Residential customer on Wakwella Road
3. **H.D. Silva** (LECO-GL-003) - Residential customer in Akmeemana
4. **Galle Heritage Hotel** (LECO-GL-004) - Commercial customer in Galle Fort
5. **M.A. Rajapaksha** (LECO-GL-005) - Residential customer in Hikkaduwa
6. **Ceylon Tea Factory** (LECO-GL-006) - Industrial customer in Bataduwa
7. **R.P. Wickramasinghe** (LECO-GL-007) - Residential customer in Karapitiya
8. **Galle Medical Center** (LECO-GL-008) - Medical facility customer

### Equipment Types (8 total)
Common electrical equipment found in Sri Lankan homes and businesses:

1. **Air Conditioning Unit** (Mitsubishi MSY-JP25VF) - ~2.5 kVA
2. **Industrial Chiller** (Carrier 30HXC080) - ~80.0 kVA
3. **Water Heater** (Abans AWH-50L) - ~3.0 kVA
4. **Ceiling Fan** (Singer SF-1200) - ~0.075 kVA
5. **Refrigerator** (Samsung RT28M3022S8) - ~0.15 kVA
6. **Industrial Motor** (ABB M3BP160M) - ~15.0 kVA
7. **LED Light Panel** (Philips CoreLine Panel) - ~0.036 kVA
8. **Washing Machine** (LG WM3488HW) - ~2.2 kVA

### Usage Records (124 total)
- **Time Period**: 30 days of historical data
- **Daily Records**: 3-6 usage records per day
- **Usage Times**: Realistic operating hours (6 AM - 8 PM mainly)
- **Duration**: 1-6 hours per usage session
- **kVA Values**: Based on equipment specifications with ±10% variation

## Data Characteristics

### Geographic Context
- **Location**: Galle District, Southern Province, Sri Lanka
- **Areas Covered**: Galle Fort, Wakwella, Akmeemana, Hikkaduwa, Bataduwa, Karapitiya
- **Customer Mix**: Residential (62.5%), Commercial (25%), Industrial (12.5%)

### Power Usage Patterns
- **Peak Hours**: 8 AM - 6 PM (business hours)
- **Equipment Distribution**: Mix of high-power industrial equipment and low-power household items
- **Seasonal Variation**: Data includes realistic fluctuations in power consumption

### Data Quality Features
- **Realistic Values**: kVA ratings based on actual equipment specifications
- **Time Consistency**: Logical start and end times for usage sessions
- **Customer Diversity**: Different types of consumers with varied usage patterns
- **Equipment Variety**: From low-power LED panels (0.036 kVA) to industrial chillers (80.0 kVA)

## Usage Scenarios

### For Management
- Monitor overall power consumption trends
- Identify high-usage customers and equipment
- Analyze usage patterns by customer type
- Generate reports for billing and planning

### For Data Entry Users
- Practice adding new usage records
- Understand typical kVA values for different equipment
- Learn proper time and date entry procedures

### For System Testing
- Validate filtering and search functionality
- Test report generation capabilities
- Verify role-based access controls
- Demonstrate system scalability

## Sri Lankan Context

### Local Brands Included
- **Abans**: Popular Sri Lankan appliance brand
- **Singer**: Well-known electronics brand in Sri Lanka
- **International Brands**: Mitsubishi, Samsung, LG, Philips (commonly available)

### Power Grid Considerations
- **kVA Values**: Appropriate for Sri Lankan electrical standards
- **Usage Patterns**: Reflect tropical climate needs (AC, fans)
- **Industrial Equipment**: Suitable for tea processing and manufacturing

## Data Refresh
To regenerate sample data:
1. Clear existing data if needed
2. Run the sample data creation commands
3. Verify data integrity through the admin panel

This sample data provides a realistic foundation for testing and demonstrating the PowerCollect system's capabilities in a Sri Lankan power distribution context.
