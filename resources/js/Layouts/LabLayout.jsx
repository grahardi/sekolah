import PublicNavbar from '../Components/PublicNavbar';
import PublicFooter from '../Components/PublicFooter';

export default function LabLayout({ children, title, breadcrumb = [] }) {
    return (
        <div className="min-h-screen bg-cream text-navy flex flex-col">
            <PublicNavbar />

            <div className="max-w-6xl w-full mx-auto px-6 lg:px-8 py-8 flex-1">
                {breadcrumb.length > 0 && (
                    <p className="text-xs text-navy/50 font-mono mb-1">{breadcrumb.join(' / ')}</p>
                )}
                <h1 className="font-display font-700 text-2xl lg:text-3xl text-navy mb-6">{title}</h1>
                {children}
            </div>

            <PublicFooter />
        </div>
    );
}
