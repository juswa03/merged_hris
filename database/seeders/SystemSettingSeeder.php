<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SystemSetting;

class SystemSettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // --- General / University ---
            ['key' => 'university_name',    'name' => 'University Name',    'value' => 'Biliran Province State University', 'type' => 'string',  'group' => 'general', 'description' => 'Full official name of the university'],
            ['key' => 'university_acronym', 'name' => 'Acronym',            'value' => 'BIPSU',                              'type' => 'string',  'group' => 'general', 'description' => 'University acronym'],
            ['key' => 'university_address', 'name' => 'Address',            'value' => 'Naval, Biliran, Philippines',         'type' => 'string',  'group' => 'general', 'description' => 'University address'],
            ['key' => 'university_email',   'name' => 'Contact Email',      'value' => 'info@bipsu.edu.ph',                  'type' => 'string',  'group' => 'general', 'description' => 'Primary contact email'],
            ['key' => 'university_phone',   'name' => 'Phone Number',       'value' => '+63 999 999 9999',                   'type' => 'string',  'group' => 'general', 'description' => 'Contact phone number'],
            ['key' => 'university_website', 'name' => 'Website',            'value' => 'https://www.bipsu.edu.ph',           'type' => 'string',  'group' => 'general', 'description' => 'Official university website'],

            // --- Attendance ---
            ['key' => 'work_start_time',        'name' => 'Work Start Time',        'value' => '08:00', 'type' => 'string',  'group' => 'attendance', 'description' => 'Official start of work (HH:MM)'],
            ['key' => 'work_end_time',          'name' => 'Work End Time',          'value' => '17:00', 'type' => 'string',  'group' => 'attendance', 'description' => 'Official end of work (HH:MM)'],
            ['key' => 'grace_period_minutes',   'name' => 'Grace Period (minutes)', 'value' => '15',    'type' => 'number',  'group' => 'attendance', 'description' => 'Minutes allowed before marking late'],
            ['key' => 'work_hours_per_day',     'name' => 'Work Hours Per Day',     'value' => '8',     'type' => 'number',  'group' => 'attendance', 'description' => 'Required work hours per day'],
            ['key' => 'work_days_per_week',     'name' => 'Work Days Per Week',     'value' => '5',     'type' => 'number',  'group' => 'attendance', 'description' => 'Required work days per week'],
            ['key' => 'overtime_enabled',       'name' => 'Enable Overtime',        'value' => '1',     'type' => 'boolean', 'group' => 'attendance', 'description' => 'Allow overtime tracking'],
            ['key' => 'overtime_rate_multiplier','name' => 'Overtime Rate Multiplier','value' => '1.25', 'type' => 'number',  'group' => 'attendance', 'description' => 'Multiplier applied to hourly rate for overtime pay'],

            // --- Leave ---
            ['key' => 'vacation_leave_days',   'name' => 'Vacation Leave (days/year)',    'value' => '15', 'type' => 'number',  'group' => 'leave', 'description' => 'Vacation leave allowance per year'],
            ['key' => 'sick_leave_days',       'name' => 'Sick Leave (days/year)',        'value' => '15', 'type' => 'number',  'group' => 'leave', 'description' => 'Sick leave allowance per year'],
            ['key' => 'mandatory_leave_days',  'name' => 'Mandatory Leave (days/year)',   'value' => '5',  'type' => 'number',  'group' => 'leave', 'description' => 'Mandatory leave per year'],
            ['key' => 'leave_carry_forward',   'name' => 'Allow Leave Carry-Forward',     'value' => '1',  'type' => 'boolean', 'group' => 'leave', 'description' => 'Allow unused leave to carry over to next year'],
            ['key' => 'max_leave_carry_days',  'name' => 'Max Carry-Forward Days',        'value' => '10', 'type' => 'number',  'group' => 'leave', 'description' => 'Maximum days that can be carried forward'],

            // --- Email ---
            ['key' => 'email_notifications_enabled', 'name' => 'Enable Email Notifications', 'value' => '1',                    'type' => 'boolean', 'group' => 'email', 'description' => 'Send email notifications system-wide'],
            ['key' => 'mail_from_address',           'name' => 'From Email Address',          'value' => 'noreply@bipsu.edu.ph', 'type' => 'string',  'group' => 'email', 'description' => 'Sender email address for system emails'],
            ['key' => 'mail_from_name',              'name' => 'From Name',                   'value' => 'BIPSU HRIS',           'type' => 'string',  'group' => 'email', 'description' => 'Sender display name for system emails'],
            ['key' => 'payslip_email_enabled',       'name' => 'Send Payslip Emails',         'value' => '1',                    'type' => 'boolean', 'group' => 'email', 'description' => 'Automatically email payslips when generated'],
            ['key' => 'leave_email_notifications',   'name' => 'Leave Email Alerts',          'value' => '1',                    'type' => 'boolean', 'group' => 'email', 'description' => 'Send email alerts for leave requests and approvals'],
        ];

        foreach ($settings as $setting) {
            SystemSetting::updateOrCreate(['key' => $setting['key']], $setting);
        }

        $this->command->info('System settings seeded successfully.');
    }
}
