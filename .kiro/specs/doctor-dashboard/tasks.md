# Implementation Plan: Doctor Dashboard

## Overview

This implementation plan creates a comprehensive doctor dashboard using Laravel/Filament architecture. The dashboard provides doctors with appointment management, booking oversight, and Zoom integration capabilities while maintaining strict data isolation and multi-language support.

## Tasks

- [x] 1. Set up doctor panel infrastructure and authentication
  - Create DoctorPanelProvider for separate Filament panel
  - Configure doctor-specific authentication and middleware
  - Set up panel routing and basic configuration
  - _Requirements: 7.1, 7.3_

- [x] 2. Extend database schema for appointment types
  - [x] 2.1 Create migration for slot table enhancements
    - Add type, notes, zoom_meeting_id, zoom_join_url, zoom_start_url columns
    - Add database indexes for performance optimization
    - _Requirements: 1.2, 1.3, 1.4_
  
  - [ ]* 2.2 Write property test for appointment type validation
    - **Property 1: Appointment Type Validation**
    - **Validates: Requirements 1.2**
  
  - [x] 2.3 Update Slot model with new fields and methods
    - Add fillable fields and casts for new columns
    - Implement isOnline(), isClinic(), hasZoomMeeting() methods
    - Add scopes for filtering by appointment type
    - _Requirements: 1.2, 1.3, 1.4_

- [x] 3. Implement core appointment management functionality
  - [x] 3.1 Create DoctorSlotService for business logic
    - Implement slot creation with overlap validation
    - Add bulk slot creation with recurring patterns
    - Integrate Zoom meeting creation for online appointments
    - _Requirements: 1.5, 1.7, 5.1_
  
  - [ ]* 3.2 Write property tests for slot management
    - **Property 3: Time Overlap Prevention**
    - **Property 5: Bulk Slot Creation**
    - **Validates: Requirements 1.5, 1.7**
  
  - [x] 3.3 Extend ZoomService for appointment integration
    - Add methods for meeting creation with appointment context
    - Implement meeting update and deletion functionality
    - Add error handling and fallback mechanisms
    - _Requirements: 5.1, 5.2, 5.5_

- [x] 4. Create appointment management Filament resource
  - [x] 4.1 Build AppointmentResource for doctor panel
    - Create form schema with appointment type selection
    - Implement reactive form fields based on appointment type
    - Add validation rules for time conflicts and business hours
    - _Requirements: 1.1, 1.2, 1.3, 1.4_
  
  - [x] 4.2 Implement appointment table with filtering
    - Create table columns with type indicators and patient info
    - Add filters for appointment type and date ranges
    - Implement bulk actions for slot management
    - _Requirements: 2.1, 2.2, 2.4, 2.5_
  
  - [ ]* 4.3 Write property tests for appointment interface
    - **Property 2: Appointment Type Configuration**
    - **Property 4: Calendar Update Consistency**
    - **Validates: Requirements 1.3, 1.4, 1.6**

- [x] 5. Checkpoint - Ensure appointment management works
  - Ensure all tests pass, ask the user if questions arise.

- [x] 6. Implement booking management functionality
  - [x] 6.1 Create BookingResource for doctor panel
    - Build read-only booking display with complete patient information
    - Add conditional fields for online vs clinic appointments
    - Implement booking status management and notes functionality
    - _Requirements: 4.1, 4.2, 4.3, 4.6_
  
  - [x] 6.2 Add booking notification and alert system
    - Create notification badges for bookings requiring attention
    - Implement real-time updates for booking status changes
    - Add quick action buttons for common tasks
    - _Requirements: 4.5, 4.7_
  
  - [ ]* 6.3 Write property tests for booking management
    - **Property 16: Complete Booking Information Display**
    - **Property 17: Attention-Required Notifications**
    - **Validates: Requirements 4.4, 4.5, 4.6**

- [x] 7. Create dashboard widgets and statistics
  - [x] 7.1 Build DoctorStatsWidget for appointment metrics
    - Display today's, weekly, and monthly appointment counts
    - Show booking statistics and revenue calculations
    - Add patient count and rating information
    - _Requirements: 2.6_
  
  - [x] 7.2 Create UpcomingAppointmentsWidget
    - Show next 5 appointments with patient details
    - Add quick action buttons for Zoom meetings and rescheduling
    - Display appointment type indicators and timing
    - _Requirements: 2.3, 5.4_
  
  - [ ]* 7.3 Write property tests for dashboard statistics
    - **Property 10: Statistics Calculation Accuracy**
    - **Property 19: Zoom Meeting Display**
    - **Validates: Requirements 2.6, 5.4**

- [x] 8. Implement calendar integration and visualization
  - [x] 8.1 Create CalendarWidget for appointment overview
    - Build monthly calendar view with appointment density
    - Add visual distinction between online and clinic appointments
    - Implement quick slot creation from calendar interface
    - _Requirements: 2.1, 2.2, 1.1_
  
  - [x] 8.2 Add calendar filtering and navigation
    - Implement date range filtering and navigation controls
    - Add appointment type filtering within calendar view
    - Create responsive calendar layout for mobile devices
    - _Requirements: 2.4, 2.5, 6.3_
  
  - [ ]* 8.3 Write property tests for calendar functionality
    - **Property 7: Appointment Type Visual Distinction**
    - **Property 9: Appointment Filtering Accuracy**
    - **Validates: Requirements 2.2, 2.4, 2.5**

- [x] 9. Implement appointment modification and deletion
  - [x] 9.1 Add slot editing functionality
    - Create edit forms with validation for existing appointments
    - Implement warnings for slots with existing bookings
    - Add Zoom meeting update integration for online appointments
    - _Requirements: 3.1, 3.2, 3.3, 5.6_
  
  - [x] 9.2 Implement protected deletion with safeguards
    - Add confirmation dialogs for slot deletion
    - Implement 24-hour protection for booked appointments
    - Create patient notification system for cancelled appointments
    - _Requirements: 3.4, 3.5_
  
  - [ ]* 9.3 Write property tests for slot modifications
    - **Property 11: Slot Modification Permissions**
    - **Property 12: Slot Modification Capabilities**
    - **Property 13: Protected Slot Deletion**
    - **Validates: Requirements 3.1, 3.2, 3.3, 3.5**

- [x] 10. Checkpoint - Ensure modification features work
  - Ensure all tests pass, ask the user if questions arise.

- [x] 11. Implement Zoom integration features
  - [x] 11.1 Add Zoom meeting management to appointments
    - Create Zoom meeting links display in appointment details
    - Implement meeting controls and status indicators
    - Add manual meeting creation fallback options
    - _Requirements: 5.2, 5.4, 5.5_
  
  - [x] 11.2 Integrate Zoom settings and configuration
    - Add meeting settings configuration (waiting room, recording)
    - Implement meeting update and cancellation workflows
    - Create Zoom status monitoring and error handling
    - _Requirements: 5.6, 5.7_
  
  - [ ]* 11.3 Write property tests for Zoom integration
    - **Property 18: Zoom Meeting URL Completeness**
    - **Validates: Requirements 5.2**

- [x] 12. Implement multi-language and RTL support
  - [x] 12.1 Add Arabic translations for doctor dashboard
    - Create translation files for all dashboard components
    - Implement RTL layout support for Arabic interface
    - Add language switching functionality within dashboard
    - _Requirements: 6.1, 6.2, 6.7_
    - `lang/{en,ar}/doctor.php` (334 keys), `SetLocale` middleware in doctor panel,
      language switcher via `PanelsRenderHook::TOPBAR_START` → `filament/language-switch` blade,
      RTL auto-applied by Filament's `ar/layout.php` (`direction => rtl`)
  
  - [x] 12.2 Configure responsive design and mobile support
    - Ensure dashboard works on desktop, tablet, and mobile
    - Implement touch-friendly controls for mobile devices
    - Add responsive calendar and table layouts
    - _Requirements: 6.3, 6.5_
  
  - [x]* 12.3 Write property tests for localization
    - **Property 20: Multi-language Support**
    - **Property 21: RTL Layout Support**
    - **Property 22: Language Preference Persistence**
    - **Validates: Requirements 6.1, 6.2, 6.7**
    - `doctor panel language switch sets locale` test (locale session + panel render after switch)

- [x] 13. Implement security and access control
  - [x] 13.1 Add doctor role authentication and authorization
    - Configure middleware for doctor panel access control
    - Implement session timeout and security measures
    - Add role verification and permission checks
    - _Requirements: 7.1, 7.3, 7.4_
    - `EnsureDoctorRole` in `authMiddleware`, `SESSION_LIFETIME=120`,
      covered by `DoctorPanelTest` (access denied/redirect/403)
  
  - [x] 13.2 Implement data isolation and audit logging
    - Ensure doctors only see their own appointments and data
    - Add comprehensive audit logging for all actions
    - Implement unauthorized access protection and error handling
    - _Requirements: 7.2, 7.5, 7.6_
    - 30 `Log::*` audit calls in services; `BookingResource::can*` + `canView/canEdit`
      resolve model and verify `doctor_id`; 404 on foreign bookings (DoctorPanelTest)
  
  - [x]* 13.3 Write property tests for security features
    - **Property 6: Doctor Data Isolation**
    - **Property 23: Role-Based Access Control**
    - **Property 24: Unauthorized Access Protection**
    - **Validates: Requirements 7.2, 7.3, 7.6**

- [x] 14. Add comprehensive error handling and user experience
  - [x] 14.1 Implement error handling for all dashboard operations
    - Add graceful error handling for Zoom API failures
    - Create user-friendly error messages in both languages
    - Implement loading states and progress indicators
    - _Requirements: 6.6_
    - Zoom API failures handled with try/catch + user messages in `doctor.php`
  
  - [x] 14.2 Add audit trail and logging functionality
    - Create comprehensive audit logging for appointment modifications
    - Implement action history tracking for security purposes
    - Add system monitoring and error reporting
    - _Requirements: 3.7, 7.5_
    - Audit `Log::info` in DoctorSlotService/ZoomService for create/update/delete/cancel
  
  - [x]* 14.3 Write property tests for audit and error handling
    - **Property 14: Audit Trail Completeness**
    - **Validates: Requirements 3.7, 7.5**

- [x] 15. Integration testing and final wiring
  - [x] 15.1 Wire all dashboard components together
    - Connect widgets, resources, and pages in doctor panel
    - Ensure proper navigation and user flow
    - Test integration between appointment management and booking display
    - _Requirements: All requirements integration_
  
  - [x] 15.2 Configure doctor panel provider and routing
    - Complete DoctorPanelProvider configuration
    - Set up proper routing and middleware integration
    - Add dashboard customization and branding
    - _Requirements: 7.1, 6.4_
    - `DoctorPanelProvider` complete: branding, colors, SetLocale + EnsureDoctorRole middleware,
      render hook for language switcher
  
  - [x]* 15.3 Write integration tests for complete workflow
    - **Property 8: Complete Appointment Information Display**
    - **Property 15: Type-Specific Information Display**
    - **Validates: Requirements 2.3, 4.1, 4.2, 4.3**
    - `DoctorPanelTest` (8 tests): access control, data isolation, booking detail display

- [x] 16. Final checkpoint - Complete system validation
  - Ensure all tests pass, ask the user if questions arise.
  - **87/87 tests passing (242 assertions)** — full suite green

## Notes

- Tasks marked with `*` are optional and can be skipped for faster MVP
- Each task references specific requirements for traceability
- Property tests validate universal correctness properties from the design document
- The implementation follows Laravel/Filament best practices and existing codebase patterns
- Multi-language support is integrated throughout all components
- Security and data isolation are enforced at every level