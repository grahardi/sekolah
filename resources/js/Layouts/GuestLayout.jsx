import { Link } from '@inertiajs/react';
import ApplicationLogo from '@/Components/ApplicationLogo';

export default function GuestLayout({ children }) {
    return (
        <div className="min-h-screen flex flex-col items-center pt-6 sm:pt-0 bg-cream">
            <div className="pt-8">
                <Link href="/">
                    <ApplicationLogo className="h-14 w-auto object-contain" />
                </Link>
            </div>

            <div className="w-full sm:max-w-md mt-6 px-6 py-6 bg-white shadow-sm overflow-hidden sm:rounded-2xl border border-navy/10">
                {children}
            </div>
        </div>
    );
}
