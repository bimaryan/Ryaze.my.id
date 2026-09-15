import DashboardLayout from '../../../Layouts/DashboardLayout';

const sections = [
    {
        title: 'Getting Started',
        icon: 'fa-solid fa-rocket',
        content: [
            { heading: 'Welcome to Ryaze Hosting', text: 'Ryaze Hosting provides a fast and reliable platform to deploy your web applications. Deploy projects in seconds with our one-click templates.' },
            { heading: 'Your First Deployment', text: 'Navigate to the Deploy page, choose a template (Laravel, Node.js, Static HTML, etc.), enter your project name, and click Deploy. Your project will be live within minutes.' },
            { heading: 'Project Structure', text: 'Each project gets its own isolated environment with a unique subdomain. You can attach a custom domain, manage environment variables, and monitor resource usage from the dashboard.' },
        ]
    },
    {
        title: 'Deployment',
        icon: 'fa-solid fa-cloud-arrow-up',
        content: [
            { heading: 'Available Templates', text: 'We support Laravel, Node.js, Next.js, PHP, Python, Django, WordPress, and static HTML sites. More templates are added regularly.' },
            { heading: 'Environment Variables', text: 'Configure your application\'s environment variables from the project detail page. Changes are applied immediately on the next rebuild.' },
            { heading: 'Rebuilding', text: 'After making changes to environment variables or WAF rules, click the Rebuild button to apply changes. The build process typically takes 1-3 minutes.' },
        ]
    },
    {
        title: 'Custom Domain',
        icon: 'fa-solid fa-globe',
        content: [
            { heading: 'Adding a Custom Domain', text: 'Go to your project settings and enter your custom domain in the domain field. You will receive DNS configuration instructions.' },
            { heading: 'DNS Configuration', text: 'Point your domain\'s A record or CNAME to the provided target. DNS propagation may take up to 48 hours, though it often completes within minutes.' },
            { heading: 'SSL Certificate', text: 'SSL certificates are automatically provisioned and renewed for all custom domains. No manual configuration is required.' },
        ]
    },
    {
        title: 'Databases',
        icon: 'fa-solid fa-database',
        content: [
            { heading: 'Database Types', text: 'We support MySQL, PostgreSQL, and MongoDB databases. Create databases from the Databases page in your dashboard.' },
            { heading: 'Connecting', text: 'Use the host, port, username, and password provided when creating the database. Connect using your preferred database client or from your application code.' },
            { heading: 'phpMyAdmin', text: 'Access the built-in database manager from the DB Manager page for a web-based interface to manage your databases.' },
        ]
    },
    {
        title: 'Storage',
        icon: 'fa-solid fa-hard-drive',
        content: [
            { heading: 'Disk Usage', text: 'Monitor your storage usage from the Storage page. Each plan has a storage limit that can be viewed on the Subscription page.' },
            { heading: 'File Management', text: 'Project files are accessible via the project detail page. View storage breakdown per project and manage files directly.' },
        ]
    },
    {
        title: 'Email',
        icon: 'fa-solid fa-envelope',
        content: [
            { heading: 'Email Accounts', text: 'Create email accounts for your custom domain from the Emails page. Each account comes with webmail access and IMAP/SMTP support.' },
            { heading: 'Quotas', text: 'Email quotas are set per account and can be managed from the email settings. Upgrade your plan for higher quotas.' },
        ]
    },
    {
        title: 'Web to APK',
        icon: 'fa-brands fa-android',
        content: [
            { heading: 'Convert Website to APK', text: 'Use the Web to APK tool to convert any website into an Android APK file. Enter your website URL, app name, and upload an icon.' },
            { heading: 'Build Process', text: 'After submitting, the build process begins. Monitor progress in real-time and download the APK once complete.' },
        ]
    },
    {
        title: 'Tunnels',
        icon: 'fa-solid fa-network-wired',
        content: [
            { heading: 'What are Tunnels?', text: 'Tunnels allow you to expose a local port to the public internet. Useful for development, webhooks, and testing.' },
            { heading: 'Creating a Tunnel', text: 'Navigate to the Tunnels page, click Create, and specify the local port. A public URL will be generated instantly.' },
        ]
    },
];

export default function Docs() {
    return (
        <DashboardLayout title="Documentation">
            <div className="space-y-6">
                <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] p-6">
                    <h1 className="text-lg font-bold text-[#333] dark:text-white mb-2">Ryaze Hosting Documentation</h1>
                    <p className="text-[13px] text-[#666] dark:text-white/60">Everything you need to deploy and manage your web applications.</p>
                </div>

                <div className="grid grid-cols-1 lg:grid-cols-4 gap-6">
                    <div className="lg:col-span-1">
                        <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] p-4 sticky top-20">
                            <h3 className="text-[11px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider mb-3">Contents</h3>
                            <nav className="space-y-1">
                                {sections.map((s, i) => (
                                    <a key={i} href={`#section-${i}`} className="flex items-center gap-2 px-3 py-2 text-[13px] text-[#666] dark:text-white/60 hover:text-[#7c3aed] hover:bg-[#f5f0ff] dark:hover:bg-white/5 transition-colors">
                                        <i className={`${s.icon} text-xs w-4 text-center`}></i>
                                        {s.title}
                                    </a>
                                ))}
                            </nav>
                        </div>
                    </div>

                    <div className="lg:col-span-3 space-y-4">
                        {sections.map((section, si) => (
                            <div key={si} id={`section-${si}`} className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e]">
                                <div className="px-5 py-4 border-b border-[#e5e5e5] dark:border-[#1a1a2e] flex items-center gap-2">
                                    <i className={`${section.icon} text-[#7c3aed]`}></i>
                                    <h2 className="text-[14px] font-bold text-[#333] dark:text-white">{section.title}</h2>
                                </div>
                                <div className="divide-y divide-[#e5e5e5] dark:divide-[#1a1a2e]">
                                    {section.content.map((item, ci) => (
                                        <div key={ci} className="px-5 py-4">
                                            <h3 className="text-[13px] font-bold text-[#333] dark:text-white mb-1">{item.heading}</h3>
                                            <p className="text-[13px] text-[#666] dark:text-white/60 leading-relaxed">{item.text}</p>
                                        </div>
                                    ))}
                                </div>
                            </div>
                        ))}
                    </div>
                </div>
            </div>
        </DashboardLayout>
    );
}
