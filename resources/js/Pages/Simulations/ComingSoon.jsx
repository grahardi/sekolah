import { Link } from '@inertiajs/react';
import LabLayout from '../../Layouts/LabLayout';

export default function ComingSoon({ title, category, subject }) {
    return (
        <LabLayout title={title} breadcrumb={['Home', 'Simulasi', category]}>
            <div className="rounded-2xl bg-white border border-navy/10 p-10 text-center max-w-xl mx-auto">
                <span className="inline-block text-xs font-mono uppercase tracking-wide text-teal bg-teal-light rounded-full px-3 py-1 mb-4">
                    Segera Hadir
                </span>
                <h2 className="font-display font-600 text-xl text-navy">{title}</h2>
                <p className="text-sm text-navy/60 mt-2">{subject}</p>
                <p className="text-navy/60 mt-4">
                    Simulasi ini sedang dalam pengembangan. Sementara itu, coba dulu modul
                    yang sudah aktif di kategori Fisika.
                </p>
                <Link
                    href="/lab"
                    className="inline-flex items-center gap-2 mt-6 bg-teal text-white font-medium rounded-lg px-5 py-2.5 text-sm hover:brightness-110"
                >
                    &larr; Kembali ke Katalog Simulasi
                </Link>
            </div>
        </LabLayout>
    );
}
