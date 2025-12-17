<?php

namespace App\Console\Commands;

use App\Models\HealthProfessional;
use App\Models\Service;
use Illuminate\Console\Command;

class SeedRealisticData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:seed-realistic';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed database with realistic healthcare services and professionals';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Starting to seed realistic data...');

        $this->seedServices();
        $this->seedHealthProfessionals();

        $this->info('✓ Database seeded successfully!');
        $this->newLine();
        $this->info('Created:');
        $this->info('  - 20 Services');
        $this->info('  - 20 Health Professionals');

        return Command::SUCCESS;
    }

    /**
     * Seed realistic healthcare services.
     */
    private function seedServices(): void
    {
        $this->info('Seeding services...');

        $services = [
            [
                'name' => 'General Consultation',
                'description' => 'Comprehensive medical evaluation and health assessment with a general practitioner',
                'duration_minutes' => 30,
                'price' => 75.00,
            ],
            [
                'name' => 'Cardiology Consultation',
                'description' => 'Specialized cardiovascular examination including heart health assessment and ECG',
                'duration_minutes' => 45,
                'price' => 150.00,
            ],
            [
                'name' => 'Dental Checkup & Cleaning',
                'description' => 'Routine dental examination, professional cleaning, and oral health assessment',
                'duration_minutes' => 60,
                'price' => 95.00,
            ],
            [
                'name' => 'Pediatric Consultation',
                'description' => 'Complete health assessment for children including growth and development monitoring',
                'duration_minutes' => 40,
                'price' => 85.00,
            ],
            [
                'name' => 'Physiotherapy Session',
                'description' => 'Personalized physical therapy treatment for injury recovery and pain management',
                'duration_minutes' => 50,
                'price' => 90.00,
            ],
            [
                'name' => 'Dermatology Consultation',
                'description' => 'Skin condition evaluation, diagnosis, and treatment planning',
                'duration_minutes' => 30,
                'price' => 120.00,
            ],
            [
                'name' => 'Mental Health Counseling',
                'description' => 'Professional psychological counseling and mental wellness support',
                'duration_minutes' => 60,
                'price' => 110.00,
            ],
            [
                'name' => 'Nutrition Consultation',
                'description' => 'Personalized dietary planning and nutritional guidance',
                'duration_minutes' => 45,
                'price' => 80.00,
            ],
            [
                'name' => 'Orthopedic Consultation',
                'description' => 'Specialized evaluation of bone, joint, and musculoskeletal conditions',
                'duration_minutes' => 40,
                'price' => 140.00,
            ],
            [
                'name' => 'Eye Examination',
                'description' => 'Comprehensive vision testing and eye health evaluation',
                'duration_minutes' => 35,
                'price' => 100.00,
            ],
            [
                'name' => 'Prenatal Checkup',
                'description' => 'Routine pregnancy monitoring and maternal health assessment',
                'duration_minutes' => 30,
                'price' => 95.00,
            ],
            [
                'name' => 'Allergy Testing',
                'description' => 'Comprehensive allergy identification through skin prick or blood tests',
                'duration_minutes' => 90,
                'price' => 180.00,
            ],
            [
                'name' => 'Vaccination Service',
                'description' => 'Immunization administration for adults and children',
                'duration_minutes' => 15,
                'price' => 45.00,
            ],
            [
                'name' => 'Blood Pressure Monitoring',
                'description' => 'Regular blood pressure check and hypertension management',
                'duration_minutes' => 20,
                'price' => 40.00,
            ],
            [
                'name' => 'Diabetes Management',
                'description' => 'Blood sugar monitoring, insulin adjustment, and diabetes care planning',
                'duration_minutes' => 40,
                'price' => 105.00,
            ],
            [
                'name' => 'X-Ray Imaging',
                'description' => 'Digital radiography for diagnostic imaging',
                'duration_minutes' => 25,
                'price' => 125.00,
            ],
            [
                'name' => 'Laboratory Tests',
                'description' => 'Comprehensive blood work and diagnostic laboratory testing',
                'duration_minutes' => 15,
                'price' => 85.00,
            ],
            [
                'name' => 'Respiratory Therapy',
                'description' => 'Treatment for breathing disorders and respiratory conditions',
                'duration_minutes' => 45,
                'price' => 95.00,
            ],
            [
                'name' => 'Sports Medicine Consultation',
                'description' => 'Athletic injury assessment and sports performance optimization',
                'duration_minutes' => 50,
                'price' => 130.00,
            ],
            [
                'name' => 'Geriatric Care Assessment',
                'description' => 'Comprehensive health evaluation specialized for elderly patients',
                'duration_minutes' => 60,
                'price' => 115.00,
            ],
        ];

        foreach ($services as $service) {
            Service::create($service);
        }

        $this->info('✓ Created 20 services');
    }

    /**
     * Seed realistic health professionals.
     */
    private function seedHealthProfessionals(): void
    {
        $this->info('Seeding health professionals...');

        $professionals = [
            [
                'name' => 'Dr. Sarah Anderson',
                'specialization' => 'General Practitioner',
                'email' => 'sarah.anderson@clinic.com',
                'phone' => '+1-555-0101',
            ],
            [
                'name' => 'Dr. Michael Chen',
                'specialization' => 'Cardiologist',
                'email' => 'michael.chen@clinic.com',
                'phone' => '+1-555-0102',
            ],
            [
                'name' => 'Dr. Emily Rodriguez',
                'specialization' => 'Pediatrician',
                'email' => 'emily.rodriguez@clinic.com',
                'phone' => '+1-555-0103',
            ],
            [
                'name' => 'Dr. James Wilson',
                'specialization' => 'Dentist',
                'email' => 'james.wilson@clinic.com',
                'phone' => '+1-555-0104',
            ],
            [
                'name' => 'Dr. Lisa Thompson',
                'specialization' => 'Dermatologist',
                'email' => 'lisa.thompson@clinic.com',
                'phone' => '+1-555-0105',
            ],
            [
                'name' => 'Dr. Robert Martinez',
                'specialization' => 'Orthopedic Surgeon',
                'email' => 'robert.martinez@clinic.com',
                'phone' => '+1-555-0106',
            ],
            [
                'name' => 'Dr. Jennifer Lee',
                'specialization' => 'Psychologist',
                'email' => 'jennifer.lee@clinic.com',
                'phone' => '+1-555-0107',
            ],
            [
                'name' => 'Dr. David Brown',
                'specialization' => 'Ophthalmologist',
                'email' => 'david.brown@clinic.com',
                'phone' => '+1-555-0108',
            ],
            [
                'name' => 'Maria Gonzalez, PT',
                'specialization' => 'Physiotherapist',
                'email' => 'maria.gonzalez@clinic.com',
                'phone' => '+1-555-0109',
            ],
            [
                'name' => 'Dr. William Taylor',
                'specialization' => 'Neurologist',
                'email' => 'william.taylor@clinic.com',
                'phone' => '+1-555-0110',
            ],
            [
                'name' => 'Dr. Amanda White',
                'specialization' => 'Obstetrician',
                'email' => 'amanda.white@clinic.com',
                'phone' => '+1-555-0111',
            ],
            [
                'name' => 'Dr. Christopher Moore',
                'specialization' => 'Allergist',
                'email' => 'christopher.moore@clinic.com',
                'phone' => '+1-555-0112',
            ],
            [
                'name' => 'Dr. Patricia Harris',
                'specialization' => 'Endocrinologist',
                'email' => 'patricia.harris@clinic.com',
                'phone' => '+1-555-0113',
            ],
            [
                'name' => 'Dr. Daniel Kim',
                'specialization' => 'Radiologist',
                'email' => 'daniel.kim@clinic.com',
                'phone' => '+1-555-0114',
            ],
            [
                'name' => 'Dr. Jessica Clark',
                'specialization' => 'Pulmonologist',
                'email' => 'jessica.clark@clinic.com',
                'phone' => '+1-555-0115',
            ],
            [
                'name' => 'Rachel Foster, RN',
                'specialization' => 'Nurse Practitioner',
                'email' => 'rachel.foster@clinic.com',
                'phone' => '+1-555-0116',
            ],
            [
                'name' => 'Dr. Andrew Phillips',
                'specialization' => 'Sports Medicine Physician',
                'email' => 'andrew.phillips@clinic.com',
                'phone' => '+1-555-0117',
            ],
            [
                'name' => 'Dr. Michelle Turner',
                'specialization' => 'Geriatrician',
                'email' => 'michelle.turner@clinic.com',
                'phone' => '+1-555-0118',
            ],
            [
                'name' => 'Karen Stevens, RD',
                'specialization' => 'Nutritionist',
                'email' => 'karen.stevens@clinic.com',
                'phone' => '+1-555-0119',
            ],
            [
                'name' => 'Dr. Thomas Baker',
                'specialization' => 'Pathologist',
                'email' => 'thomas.baker@clinic.com',
                'phone' => '+1-555-0120',
            ],
        ];

        foreach ($professionals as $professional) {
            HealthProfessional::create($professional);
        }

        $this->info('✓ Created 20 health professionals');
    }
}
