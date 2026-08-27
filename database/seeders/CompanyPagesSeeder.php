<?php

namespace Database\Seeders;

use App\Models\PageContent;
use Illuminate\Database\Seeder;

class CompanyPagesSeeder extends Seeder
{
    public function run(): void
    {
        // 1. About Us
        $aboutContent = [
            'hero' => [
                'title' => 'The Story of',
                'highlight' => 'Edvora',
                'subtitle' => 'Pioneering the future of education with passion, innovation, and a commitment to lifelong learning.',
            ],
            'mission_vision' => [
                'section_title' => 'Our Mission & Vision',
                'section_subtitle' => "The driving force behind Edvora's purpose.",
                'mission_title' => 'Our Mission',
                'mission_icon' => 'bi-bullseye',
                'mission_text' => 'To democratize education by providing world-class learning experiences that empower individuals to achieve their personal and professional goals, regardless of their background or location.',
                'vision_title' => 'Our Vision',
                'vision_icon' => 'bi-eye',
                'vision_text' => "To become the world's leading online education platform, creating a global community of lifelong learners who drive innovation and positive change in their industries and communities.",
            ],
            'timeline' => [
                'section_title' => 'Our Journey',
                'section_subtitle' => 'A timeline of our milestones and achievements.',
                'items' => [
                    [
                        'year' => '2020',
                        'title' => 'The Spark of an Idea',
                        'description' => 'Edvora was born from a vision to make tech education accessible to all. We started with a small team and a big dream to revolutionize online learning in the tech space.',
                        'color' => 'blue',
                        'icon' => 'bi-lightbulb-fill',
                    ],
                    [
                        'year' => '2021',
                        'title' => 'Reaching 1,000 Students',
                        'description' => 'A major milestone reached! We empowered our first 1,000 learners, validating our mission and fueling our drive to scale even further across the region.',
                        'color' => 'yellow',
                        'icon' => 'bi-people-fill',
                    ],
                    [
                        'year' => '2022',
                        'title' => 'Global Expansion',
                        'description' => 'We broke borders, expanding into 50+ countries. Edvora became a truly global community, connecting learners from diverse backgrounds through technology.',
                        'color' => 'green',
                        'icon' => 'bi-globe-americas',
                    ],
                    [
                        'year' => '2024',
                        'title' => 'Future of Tech Education',
                        'description' => 'Today, we lead the way with AI-integrated tools and immersive learning experiences, shaping the future of education for the next generation of innovators.',
                        'color' => 'purple',
                        'icon' => 'bi-cpu-fill',
                    ],
                ],
            ],
            'team' => [
                'section_title' => 'Meet Our Team',
                'section_subtitle' => "The passionate individuals driving Edvora's mission forward.",
                'members' => [
                    [
                        'name' => 'Mobin hassani',
                        'role' => 'Founder',
                        'bio' => "Visionary leader providing the strategic direction and inspiration behind Edvora's ecosystem.",
                        'icon' => 'bi-award-fill',
                        'linkedin' => '#',
                        'twitter' => '#',
                        'github' => '',
                        'category' => 'tech',
                    ],
                    [
                        'name' => 'Alia Sharifi',
                        'role' => 'Application Developer',
                        'bio' => 'Crafting seamless mobile and desktop application experiences with cutting-edge technologies.',
                        'icon' => 'bi-phone-vibrate',
                        'linkedin' => '#',
                        'twitter' => '',
                        'github' => '#',
                        'category' => 'tech',
                    ],
                    [
                        'name' => 'Nilofar ataie',
                        'role' => 'SEO Specialist',
                        'bio' => 'Optimizing our digital presence to ensure Edvora reaches every curious mind across the globe.',
                        'icon' => 'bi-search-heart',
                        'linkedin' => '#',
                        'twitter' => '',
                        'github' => '',
                        'category' => 'marketing',
                    ],
                    [
                        'name' => 'Ismail Farhang',
                        'role' => 'IT Support',
                        'bio' => 'Ensuring a smooth and reliable technological infrastructure for our team and students alike.',
                        'icon' => 'bi-pc-display',
                        'linkedin' => '#',
                        'twitter' => '',
                        'github' => '',
                        'category' => 'tech',
                    ],
                    [
                        'name' => 'Fatima Rahmani',
                        'role' => 'Web Developer',
                        'bio' => "Building the robust and interactive web platform that powers Edvora's learning journey.",
                        'icon' => 'bi-code-slash',
                        'linkedin' => '#',
                        'twitter' => '',
                        'github' => '#',
                        'category' => 'tech',
                    ],
                    [
                        'name' => 'Jawad Hakimi',
                        'role' => 'UI/UX Designer',
                        'bio' => 'Designing intuitive and beautiful user interfaces that make learning an absolute joy.',
                        'icon' => 'bi-palette-fill',
                        'linkedin' => '',
                        'twitter' => '',
                        'github' => '',
                        'category' => 'creative',
                    ],
                    [
                        'name' => 'Mahdi Yousefi',
                        'role' => 'Graphic Designer',
                        'bio' => 'Creating the stunning visual identity and assets that define the Edvora brand.',
                        'icon' => 'bi-brush-fill',
                        'linkedin' => '',
                        'twitter' => '',
                        'github' => '',
                        'category' => 'creative',
                    ],
                    [
                        'name' => 'Timor Sidaqat',
                        'role' => 'Video Editor',
                        'bio' => 'Producing high-quality educational videos and cinematic content for our community.',
                        'icon' => 'bi-video-fill',
                        'linkedin' => '',
                        'twitter' => '',
                        'github' => '',
                        'category' => 'creative',
                    ],
                    [
                        'name' => 'Sadaf Ahmadzai',
                        'role' => 'Digital Marketing',
                        'bio' => 'Spreading the word and engaging with our global community across all digital channels.',
                        'icon' => 'bi-megaphone-fill',
                        'linkedin' => '#',
                        'twitter' => '',
                        'github' => '',
                        'category' => 'marketing',
                    ],
                ],
            ],
            'cta' => [
                'title' => 'Ready to Start Your Learning Journey?',
                'subtitle' => 'Join thousands of students who are already transforming their careers with Edvora.',
                'button_text' => 'Get Started Today',
                'button_url' => '/register',
            ],
        ];

        // 2. Our Story
        $storyContent = [
            'hero' => [
                'badge' => 'Our Journey',
                'title_prefix' => 'The Story Behind',
                'highlight' => 'Edvora',
                'subtitle' => 'From a revolutionary idea to transforming education through technology',
            ],
            'genesis' => [
                'title' => 'The Genesis',
                'icon' => 'bi-lightbulb',
                'text' => 'Our story begins with a brilliant idea that emerged from the intersection of technology and necessity. Conceived with the dream of revolutionizing education through innovative technology, Edvora was built to empower learners everywhere.',
            ],
            'vision' => [
                'title' => 'The Vision',
                'icon' => 'bi-eye',
                'text' => "We recognized technology's power to elevate society's knowledge. Our mission became clear: create a platform that democratizes quality education and empowers every individual to reach their full potential through innovative learning experiences.",
            ],
            'team_story' => [
                'title' => 'The Edvora Team',
                'icon' => 'bi-people-fill',
                'paragraph_1' => 'Our passionate group of innovators, educators, and software engineers work around the clock to create an unmatched educational experience.',
                'paragraph_2' => 'With community at our core, we believe that collaborative learning and modern tools break barriers and build bridges to bright tech futures.',
            ],
            'mission_today' => [
                'title' => 'Our Mission Today',
                'icon' => 'bi-mortarboard',
                'text' => "Dedicated to advancing society's knowledge through cutting-edge educational technology. We believe that by providing accessible, high-quality learning experiences, we can empower individuals and communities to thrive in an increasingly digital world.",
                'stats' => [
                    ['value' => '10K+', 'label' => 'Students Empowered'],
                    ['value' => '500+', 'label' => 'Courses Available'],
                    ['value' => '98%', 'label' => 'Success Rate'],
                ],
            ],
        ];

        // 3. How We Work
        $howWeWorkContent = [
            'hero' => [
                'badge' => 'Non-Profit Organization',
                'title_prefix' => 'How We',
                'highlight' => 'Work',
                'subtitle' => 'Empowering youth through education, community, and technology',
                'cta_text' => 'Explore Programs',
                'cta_url' => '/courses',
            ],
            'mission' => [
                'title' => 'Our Non-Profit Mission',
                'description' => 'Edvora is a dedicated non-profit organization committed to empowering young minds through accessible, quality education and innovative technology solutions.',
            ],
            'values' => [
                [
                    'title' => 'Youth Empowerment',
                    'description' => 'We believe in the potential of every young person and work tirelessly to provide them with the tools and opportunities they need to succeed.',
                    'icon' => 'bi-people-fill',
                    'color' => 'blue',
                ],
                [
                    'title' => 'Community Impact',
                    'description' => 'Our work extends beyond individual learning to create positive change in communities and society as a whole.',
                    'icon' => 'bi-heart-fill',
                    'color' => 'orange',
                ],
                [
                    'title' => 'Accessibility',
                    'description' => 'We ensure that quality education is accessible to all, regardless of economic background or geographical location.',
                    'icon' => 'bi-shield-check',
                    'color' => 'purple',
                ],
            ],
            'pillars' => [
                [
                    'title' => 'Volunteer-Driven Model',
                    'subtitle' => 'Passionate Educators & Tech Leaders',
                    'description' => 'Our organization thrives on the dedication of passionate volunteers - educators, technologists, and youth advocates who believe in our mission.',
                    'icon' => 'bi-gear-fill',
                    'bullets' => [
                        ['title' => 'Expert Educators', 'desc' => 'Professional teachers volunteer their time to teach live sessions and mentor.'],
                        ['title' => 'Tech Innovators', 'desc' => 'Developers and designers contribute to open-platform development.'],
                        ['title' => 'Community Leaders', 'desc' => 'Local advocates help reach underserved communities globally.'],
                    ],
                ],
                [
                    'title' => 'Funding & Sustainability',
                    'subtitle' => 'Transparent & Impactful Support',
                    'description' => 'As a non-profit organization, we operate through grants, donations, and partnerships with educational institutions and technology companies.',
                    'icon' => 'bi-currency-dollar',
                    'bullets' => [
                        ['title' => 'Educational Grants', 'desc' => 'Government and foundation funding aimed at democratizing digital skills.'],
                        ['title' => 'Corporate Partnerships', 'desc' => 'Leading tech companies supporting our mission with resources.'],
                        ['title' => 'Community Donations', 'desc' => 'Supporters worldwide investing directly in youth education.'],
                    ],
                ],
                [
                    'title' => 'Interactive Learning Pipeline',
                    'subtitle' => 'From Fundamentals to Mastery',
                    'description' => 'Our curriculum is structured to guide every learner seamlessly from basic concepts to real-world software development and portfolio readiness.',
                    'icon' => 'bi-laptop',
                    'bullets' => [
                        ['title' => 'Hands-On Projects', 'desc' => 'Real applications built from scratch with modern frameworks.'],
                        ['title' => 'Peer Review & Quizzes', 'desc' => 'Instant feedback loops, automated code validation, and gamified XP.'],
                        ['title' => 'Verified Certificates', 'desc' => 'Recognized credentials to jumpstart tech careers and freelancing.'],
                    ],
                ],
            ],
            'impact_section' => [
                'title' => 'Our Global Impact',
                'subtitle' => "Together, we're making a difference in young lives across the globe",
            ],
            'cta' => [
                'title' => 'Join the Movement Today',
                'description' => 'Whether you want to learn, teach, or support our non-profit mission, there is a place for you at Edvora.',
                'button_text' => 'Get Started',
                'button_url' => '/register',
            ],
        ];

        // 4. Terms of Service
        $termsContent = [
            'hero' => [
                'badge' => 'Edvora Legal Framework',
                'title' => 'Terms of Service',
                'subtitle' => "Our commitment to your privacy, security, and elite learning experience. We've refined our terms to be as transparent as our platform.",
                'last_updated' => 'Dec 2024',
                'version' => 'Version 2.4.0',
            ],
            'modules' => [
                [
                    'number' => '1',
                    'title' => 'Acceptance of Terms',
                    'text' => 'By accessing and using Edvora ("the Platform"), you accept and agree to be bound by the terms and provision of this agreement. Our platform is designed to be an elite, safe, and productive environment for all.',
                    'tldr' => 'Using the site means you follow the rules. Simple.',
                    'size' => 'wide',
                    'color' => 'blue',
                    'icon' => 'bi-check2-circle',
                ],
                [
                    'number' => '2',
                    'title' => 'Our Services',
                    'text' => 'Edvora is a premium learning ecosystem providing expert-led courses, professional growth tools, and community discussion hubs. We specialize in high-quality interactive materials, global learner networking, industry-recognized certificates, and personalized progress tracking.',
                    'tldr' => 'We provide high-tech tools to help you learn and grow your professional career.',
                    'size' => 'large',
                    'color' => 'teal',
                    'icon' => 'bi-cpu',
                ],
                [
                    'number' => '3',
                    'title' => 'Account Integrity',
                    'text' => 'Your account is personal. You must maintain the confidentiality of your credentials. You are responsible for all actions taken through your account.',
                    'tldr' => "Keep your password safe and don't share your login.",
                    'size' => 'tall',
                    'color' => 'violet',
                    'icon' => 'bi-shield-lock',
                ],
                [
                    'number' => '4',
                    'title' => 'Enrollment',
                    'text' => 'Personal license to learn. Lifetime access to your enrolled courses and all future updates.',
                    'tldr' => '',
                    'size' => 'normal',
                    'color' => 'amber',
                    'icon' => 'bi-mortarboard',
                ],
                [
                    'number' => '5',
                    'title' => 'Free Access',
                    'text' => 'Edvora courses are provided free of charge. No course payment or refund process applies.',
                    'tldr' => '',
                    'size' => 'normal',
                    'color' => 'blue',
                    'icon' => 'bi-unlock',
                ],
                [
                    'number' => '6',
                    'title' => 'Community Conduct',
                    'text' => 'Respect is mandatory. No harassment, content scraping, or unauthorized redistribution of Edvora materials is allowed.',
                    'tldr' => "Be kind to others and don't steal or share our course videos.",
                    'size' => 'wide',
                    'color' => 'teal',
                    'icon' => 'bi-person-lines-fill',
                ],
                [
                    'number' => '7',
                    'title' => 'IP Rights',
                    'text' => 'Content belongs to Edvora. You have a license to learn, not to own the intellectual property.',
                    'tldr' => '',
                    'size' => 'normal',
                    'color' => 'violet',
                    'icon' => 'bi-incognito',
                ],
                [
                    'number' => '8',
                    'title' => 'Privacy Policy',
                    'text' => 'Your data is yours. We only use insights to improve your learning experience. We never sell your personal information.',
                    'tldr' => 'Your privacy is our biggest commitment.',
                    'size' => 'dark',
                    'color' => 'dark',
                    'icon' => 'bi-shield-shaded',
                ],
                [
                    'number' => '9',
                    'title' => 'Disclaimers',
                    'text' => 'Service provided "as is". We aim for 99.9% uptime and accurate content at all times.',
                    'tldr' => '',
                    'size' => 'normal',
                    'color' => 'amber',
                    'icon' => 'bi-exclamation-triangle',
                ],
                [
                    'number' => '10',
                    'title' => 'Liability',
                    'text' => 'Limitation on indirect or incidental damages related to platform usage.',
                    'tldr' => '',
                    'size' => 'normal',
                    'color' => 'blue',
                    'icon' => 'bi-file-earmark-lock',
                ],
                [
                    'number' => '11',
                    'title' => 'Termination',
                    'text' => 'We reserve the right to suspend accounts violating these terms with prior notice.',
                    'tldr' => '',
                    'size' => 'normal',
                    'color' => 'teal',
                    'icon' => 'bi-door-closed',
                ],
                [
                    'number' => '12',
                    'title' => 'Changes',
                    'text' => 'Terms may be updated. Continued use implies acceptance of new terms.',
                    'tldr' => '',
                    'size' => 'normal',
                    'color' => 'violet',
                    'icon' => 'bi-arrow-repeat',
                ],
                [
                    'number' => '13',
                    'title' => 'Governing Law',
                    'text' => 'These terms are governed by applicable law.',
                    'tldr' => '',
                    'size' => 'normal',
                    'color' => 'amber',
                    'icon' => 'bi-bank',
                ],
            ],
            'support' => [
                'title' => 'Support & Inquiries',
                'description' => 'Use the Edvora contact form for legal and general inquiries.',
                'link_text' => 'Open contact form',
                'link_url' => '/contact',
            ],
        ];

        // 5. Privacy Policy
        $privacyContent = [
            'hero' => [
                'tag' => 'Privacy Standards // 2024',
                'heading_1' => 'Safeguarding',
                'heading_2' => 'Digital Experience',
                'subtitle' => 'Trust is the core of our educational architecture. We protect your data with multi-layered glassmorphism inspired security protocols.',
                'protocol_button_text' => 'View Protocol',
            ],
            'panes' => [
                [
                    'index' => '01',
                    'title' => 'Immersive Data Collection',
                    'text' => 'When you interact with the Edvora ecosystem, we collect fragments of digital presence to curate a personalized learning path. This includes session telemetry and academic goal-setting data designed to optimize your performance.',
                    'tags' => ['Interaction Metrics', 'Bio Preferences', 'Session Heatmaps'],
                    'sub_note' => '',
                    'alignment' => 'left',
                ],
                [
                    'index' => '02',
                    'title' => 'Refined Information Usage',
                    'text' => 'Your data isn\'t just stored; it\'s utilized to refine our neural learning models. We use predictive analytics to suggest the best courses, making your educational journey as fluid and responsive as our interface design.',
                    'tags' => [],
                    'sub_note' => 'No data is sold to external entities. Ever.',
                    'alignment' => 'right',
                ],
                [
                    'index' => '03',
                    'title' => 'Military-Grade Encryption',
                    'text' => 'Every datum is shielded behind AES-256 bit encryption, wrapped in TLS 1.3 transport protocols. Our architecture is designed to be impenetrable, ensuring your academic assets remain locked within the Edvora vault.',
                    'tags' => [],
                    'sub_note' => '',
                    'alignment' => 'left',
                ],
                [
                    'index' => '04',
                    'title' => 'The Sovereignty of User Rights',
                    'text' => 'You are the master of your information. We provide intuitive controls to export, anonymize, or delete your entire history with a single interaction. Your rights are fundamental, not optional.',
                    'tags' => ['GDPR Ready', 'Full Erasure', 'Portability'],
                    'sub_note' => '',
                    'alignment' => 'right',
                ],
            ],
        ];

        PageContent::set('page_about', $aboutContent, 'About Us Page Content');
        PageContent::set('page_story', $storyContent, 'Our Story Page Content');
        PageContent::set('page_how_we_work', $howWeWorkContent, 'How We Work Page Content');
        PageContent::set('page_terms', $termsContent, 'Terms of Service Page Content');
        PageContent::set('page_privacy', $privacyContent, 'Privacy Policy Page Content');
    }
}
