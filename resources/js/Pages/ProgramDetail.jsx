import PublicNavbar from '../Components/PublicNavbar';
import PublicFooter from '../Components/PublicFooter';
import { findProgram, PROGRAMS } from '../constants/programs';

export default function ProgramDetail({ slug, programDb = null }) {
    const staticProgram = findProgram(slug);

    if (!staticProgram && !programDb) {
        return (
            <div className="min-h-screen bg-cream text-navy flex flex-col">
                <PublicNavbar />
                <div className="flex-1 flex items-center justify-center text-center px-6">
                    <div>
                        <p className="font-display font-700 text-2xl mb-2">Program tidak ditemukan</p>
                        <a href="/" className="text-teal font-medium">&larr; Kembali ke Home</a>
                    </div>
                </div>
                <PublicFooter />
            </div>
        );
    }

    // Konten dari database (diedit SuperAdmin) diprioritaskan, fallback ke
    // versi statis kalau field tertentu belum diisi di database.
    const program = {
        title: programDb?.title || staticProgram?.title,
        status: programDb?.status || staticProgram?.status,
        summary: programDb?.summary || staticProgram?.summary,
        detail: programDb?.detail || staticProgram?.detail,
        href: programDb?.href || staticProgram?.href,
        cta: programDb?.cta || staticProgram?.cta,
        demoHref: programDb?.demo_href || staticProgram?.demoHref,
    };

    return (
        <div className="min-h-screen bg-cream text-navy flex flex-col">
            <PublicNavbar />

            <section className="max-w-3xl mx-auto px-6 lg:px-8 py-14 lg:py-20 flex-1 w-full">
                <p className="text-xs font-mono text-navy/40 mb-2">
                    <a href="/" className="hover:text-teal">Home</a> / Program Sekolah / {program.title}
                </p>

                <div className="flex items-center gap-3 mb-4">
                    <span className={`text-[10px] font-mono uppercase tracking-wide rounded-full px-2.5 py-1 ${
                        program.status === 'Aktif' ? 'bg-teal-light text-teal' : 'bg-navy/5 text-navy/40'
                    }`}>
                        {program.status}
                    </span>
                </div>

                <h1 className="font-display font-700 text-3xl lg:text-4xl mb-3">{program.title}</h1>
                <p className="text-navy/60 text-lg mb-8">{program.summary}</p>

                <div className="rounded-2xl bg-white border border-navy/10 p-6 lg:p-8 mb-8">
                    <p className="text-navy/80 leading-relaxed">{program.detail}</p>

                    {program.status === 'Aktif' && program.href && (
                        <div className="flex flex-wrap gap-3 mt-6">
                            <a href={program.href} className="bg-teal text-white font-medium rounded-lg px-5 py-2.5 text-sm hover:brightness-110">
                                {program.cta} &rarr;
                            </a>
                            {program.demoHref && (
                                <a href={program.demoHref} className="border border-navy/20 text-navy font-medium rounded-lg px-5 py-2.5 text-sm hover:bg-navy/5">
                                    Coba Demo Dulu
                                </a>
                            )}
                        </div>
                    )}

                    {program.status === 'Segera' && (
                        <p className="text-xs text-navy/40 mt-5 pt-5 border-t border-navy/10">
                            Mau tahu duluan begitu program ini rilis? Daftarkan sekolahmu sekarang,
                            nanti kami kabari langsung.
                        </p>
                    )}
                </div>

                <h2 className="font-display font-600 text-sm text-navy/50 uppercase tracking-wide mb-3">Program lainnya</h2>
                <div className="grid grid-cols-2 sm:grid-cols-3 gap-3">
                    {PROGRAMS.filter((p) => p.slug !== slug).map((p) => (
                        <a key={p.slug} href={`/program/${p.slug}`} className="rounded-lg bg-white border border-navy/10 px-4 py-3 text-sm text-navy/70 hover:border-teal/40 hover:text-teal">
                            {p.title}
                        </a>
                    ))}
                </div>
            </section>

            <PublicFooter />
        </div>
    );
}
