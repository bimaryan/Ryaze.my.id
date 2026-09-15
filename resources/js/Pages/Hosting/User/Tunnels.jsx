import DashboardLayout from '../../../Layouts/DashboardLayout';
import { useForm, router } from '@inertiajs/react';
import { useState } from 'react';
import Swal from 'sweetalert2';

export default function Tunnels({ tunnels }) {
    const [showCreateModal, setShowCreateModal] = useState(false);
    const [showInstructionsModal, setShowInstructionsModal] = useState(false);
    const [showDocsModal, setShowDocsModal] = useState(false);
    const [selectedTunnel, setSelectedTunnel] = useState(null);

    const tunnelList = Array.isArray(tunnels) ? tunnels : (tunnels?.data || []);

    const { data, setData, post, processing, reset } = useForm({
        name: '',
        subdomain: '',
        target_port: '',
    });

    function handleCreate(e) {
        e.preventDefault();
        post(route('user_hosting.tunnels.store'), {
            onSuccess: () => { setShowCreateModal(false); reset(); },
        });
    }

    function handleDelete(tunnel) {
        Swal.fire({
            title: `Hapus tunnel "${tunnel.name}"?`,
            text: 'Tindakan ini tidak dapat dibatalkan.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            confirmButtonText: 'Ya, Hapus!',
        }).then((result) => {
            if (result.isConfirmed) {
                router.delete(route('user_hosting.tunnels.destroy', tunnel.id));
            }
        });
    }

    return (
        <DashboardLayout title="Tunnels">
            <div className="mb-1">
                <div className="flex items-center gap-3 mb-1">
                    <div className="w-10 h-10 bg-[#f5f0ff] dark:bg-[#7c3aed]/20 flex items-center justify-center">
                        <i className="fa-solid fa-network-wired text-[#7c3aed] dark:text-[#a78bfa]"></i>
                    </div>
                    <div>
                        <h1 className="text-lg font-bold text-[#333] dark:text-white">Tunnels</h1>
                        <p className="text-[13px] text-[#999] dark:text-white/40">Expose local server ke internet melalui tunnel.</p>
                    </div>
                    <div className="ml-auto flex items-center gap-2">
                        <button onClick={() => setShowDocsModal(true)}
                            className="inline-flex items-center gap-2 bg-slate-100 dark:bg-slate-700/50 text-[#666] dark:text-white/60 px-4 py-2 rounded-lg text-sm font-medium transition hover:bg-slate-200 dark:hover:bg-slate-600">
                            <i className="fa-solid fa-book"></i> Dokumentasi
                        </button>
                        <button onClick={() => setShowCreateModal(true)}
                            className="inline-flex items-center gap-2 bg-[#7c3aed] hover:bg-[#6d28d9] text-white px-5 py-2.5 rounded-lg text-sm font-medium transition shadow-sm">
                            + Buat Tunnel
                        </button>
                    </div>
                </div>
            </div>

            <div className="mt-6">
                {tunnelList.length === 0 ? (
                    <div className="bg-white dark:bg-[#0d0d18] rounded-2xl shadow-sm border border-[#e5e5e5] dark:border-[#1a1a2e] p-12 text-center">
                        <div className="w-16 h-16 bg-slate-100 dark:bg-slate-700/50 text-slate-400 dark:text-slate-500 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl">
                            <i className="fa-solid fa-network-wired"></i>
                        </div>
                        <h3 className="text-lg font-bold text-[#333] dark:text-white mb-2">Belum ada tunnel</h3>
                        <p className="text-[#999] dark:text-white/40 mb-6 text-sm">Buat tunnel pertama untuk mengakses local server dari internet.</p>
                        <button onClick={() => setShowCreateModal(true)}
                            className="inline-flex items-center gap-2 bg-[#7c3aed] hover:bg-[#6d28d9] text-white px-5 py-2.5 rounded-lg text-sm font-medium transition shadow-sm">
                            + Buat Tunnel
                        </button>
                    </div>
                ) : (
                    <div className="bg-white dark:bg-[#0d0d18] rounded-2xl shadow-sm border border-[#e5e5e5] dark:border-[#1a1a2e] overflow-hidden">
                        <div className="overflow-x-auto">
                            <table className="w-full text-sm text-left">
                                <thead className="text-[11px] font-bold uppercase tracking-wider text-[#999] dark:text-white/40 bg-[#fafafa] dark:bg-white/[0.02] border-b border-[#e5e5e5] dark:border-[#1a1a2e]">
                                    <tr>
                                        <th className="px-6 py-4">Nama / Subdomain</th>
                                        <th className="px-6 py-4">Target Port</th>
                                        <th className="px-6 py-4 text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-[#e5e5e5] dark:divide-[#1a1a2e]">
                                    {tunnelList.map(tunnel => (
                                        <tr key={tunnel.id} className="hover:bg-[#fafafa] dark:hover:bg-white/[0.02] transition-colors">
                                            <td className="px-6 py-4">
                                                <div className="flex items-center gap-2">
                                                    <i className="fa-solid fa-network-wired text-[#7c3aed]"></i>
                                                    <div>
                                                        <p className="font-bold text-[#333] dark:text-white text-sm">{tunnel.name}</p>
                                                        <a href={`https://${tunnel.subdomain}.ryaze.my.id`} target="_blank" rel="noopener noreferrer"
                                                            className="text-xs text-[#7c3aed] dark:text-[#a78bfa] hover:underline flex items-center gap-1">
                                                            {tunnel.subdomain}.ryaze.my.id <i className="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                            </td>
                                            <td className="px-6 py-4">
                                                <span className="inline-flex items-center px-2.5 py-1 text-xs font-bold font-mono bg-[#f5f0ff] dark:bg-[#7c3aed]/10 text-[#7c3aed] dark:text-[#a78bfa] rounded-full">
                                                    :{tunnel.target_port}
                                                </span>
                                            </td>
                                            <td className="px-6 py-4">
                                                <div className="flex items-center justify-center gap-1">
                                                    <a href={route('user_hosting.tunnels.client', tunnel.id)} target="_blank" rel="noopener noreferrer"
                                                        className="w-8 h-8 rounded-lg flex items-center justify-center text-[#7c3aed] dark:text-[#a78bfa] hover:bg-[#f5f0ff] dark:hover:bg-[#7c3aed]/10 transition" title="Download Client">
                                                        <i className="fa-solid fa-download text-sm"></i>
                                                    </a>
                                                    <button onClick={() => { setSelectedTunnel(tunnel); setShowInstructionsModal(true); }}
                                                        className="w-8 h-8 rounded-lg flex items-center justify-center text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700/50 transition" title="Instruksi">
                                                        <i className="fa-solid fa-terminal text-sm"></i>
                                                    </button>
                                                    <button onClick={() => handleDelete(tunnel)}
                                                        className="w-8 h-8 rounded-lg flex items-center justify-center text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-500/10 transition" title="Hapus">
                                                        <i className="fa-solid fa-trash text-sm"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                    </div>
                )}
            </div>

            {/* Create Modal */}
            {showCreateModal && (
                <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
                    <div className="w-full max-w-md bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-xl shadow-xl">
                        <div className="p-5 border-b border-[#e5e5e5] dark:border-[#1a1a2e] flex items-center justify-between">
                            <h3 className="font-bold text-[#333] dark:text-white text-sm">Buat Tunnel Baru</h3>
                            <button onClick={() => { setShowCreateModal(false); reset(); }} className="text-[#999] hover:text-red-500 transition">
                                <i className="fa-solid fa-xmark"></i>
                            </button>
                        </div>
                        <form onSubmit={handleCreate} className="p-5 space-y-4">
                            <div>
                                <label className="block text-xs font-bold text-[#333] dark:text-white mb-1.5">Nama Tunnel</label>
                                <input type="text" value={data.name} onChange={e => setData('name', e.target.value)} required
                                    placeholder="my-app"
                                    className="w-full bg-[#fafafa] dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-xl px-4 py-2.5 text-sm text-[#333] dark:text-white focus:ring-2 focus:ring-[#7c3aed]/20 focus:border-[#7c3aed] outline-none transition" />
                            </div>
                            <div>
                                <label className="block text-xs font-bold text-[#333] dark:text-white mb-1.5">Subdomain</label>
                                <div className="flex items-center">
                                    <input type="text" value={data.subdomain} onChange={e => setData('subdomain', e.target.value.replace(/[^a-z0-9-]/g, ''))} required
                                        placeholder="my-app"
                                        pattern="[a-z0-9-]+" minLength={3} maxLength={30}
                                        className="flex-1 bg-[#fafafa] dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-l-xl px-4 py-2.5 text-sm text-[#333] dark:text-white font-mono focus:ring-2 focus:ring-[#7c3aed]/20 focus:border-[#7c3aed] outline-none transition" />
                                    <span className="text-xs text-[#999] dark:text-white/40 bg-[#fafafa] dark:bg-white/[0.02] border border-l-0 border-[#e5e5e5] dark:border-[#1a1a2e] rounded-r-xl px-3 py-2.5 font-mono whitespace-nowrap">.ryaze.my.id</span>
                                </div>
                                <p className="text-[10px] text-[#999] dark:text-white/40 mt-1">Hanya huruf kecil, angka, dan dash.</p>
                            </div>
                            <div>
                                <label className="block text-xs font-bold text-[#333] dark:text-white mb-1.5">Target Port</label>
                                <input type="number" value={data.target_port} onChange={e => setData('target_port', e.target.value)} required
                                    min={1} max={65535} placeholder="3000"
                                    className="w-full bg-[#fafafa] dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-xl px-4 py-2.5 text-sm text-[#333] dark:text-white focus:ring-2 focus:ring-[#7c3aed]/20 focus:border-[#7c3aed] outline-none transition" />
                                <p className="text-[10px] text-[#999] dark:text-white/40 mt-1">Port local yang akan di-expose (1-65535).</p>
                            </div>
                            <div className="flex justify-end gap-3 pt-2">
                                <button type="button" onClick={() => { setShowCreateModal(false); reset(); }}
                                    className="px-4 py-2 text-sm font-medium text-[#666] dark:text-white/60 border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-lg hover:bg-[#fafafa] transition">
                                    Batal
                                </button>
                                <button type="submit" disabled={processing}
                                    className="px-4 py-2 text-sm font-medium text-white bg-[#7c3aed] rounded-lg hover:bg-[#6d28d9] transition disabled:opacity-50">
                                    {processing ? 'Membuat...' : 'Buat Tunnel'}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            )}

            {/* Instructions Modal */}
            {showInstructionsModal && selectedTunnel && (
                <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
                    <div className="w-full max-w-lg bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-xl shadow-xl">
                        <div className="p-5 border-b border-[#e5e5e5] dark:border-[#1a1a2e] flex items-center justify-between">
                            <h3 className="font-bold text-[#333] dark:text-white text-sm">Instruksi - {selectedTunnel.name}</h3>
                            <button onClick={() => setShowInstructionsModal(false)} className="text-[#999] hover:text-red-500 transition">
                                <i className="fa-solid fa-xmark"></i>
                            </button>
                        </div>
                        <div className="p-5 space-y-4 text-sm">
                            <div className="flex items-start gap-3">
                                <span className="bg-[#7c3aed] text-white rounded-full w-6 h-6 flex items-center justify-center text-xs font-bold shrink-0 mt-0.5">1</span>
                                <div>
                                    <p className="font-bold text-[#333] dark:text-white mb-1">Download Client</p>
                                    <a href={route('user_hosting.tunnels.client', selectedTunnel.id)} target="_blank" rel="noopener noreferrer"
                                        className="inline-flex items-center gap-2 text-[#7c3aed] dark:text-[#a78bfa] hover:underline text-sm">
                                        <i className="fa-solid fa-download"></i> Download PHP Client
                                    </a>
                                </div>
                            </div>
                            <div className="flex items-start gap-3">
                                <span className="bg-[#7c3aed] text-white rounded-full w-6 h-6 flex items-center justify-center text-xs font-bold shrink-0 mt-0.5">2</span>
                                <div>
                                    <p className="font-bold text-[#333] dark:text-white mb-1">Jalankan Perintah</p>
                                    <div className="bg-slate-900 text-emerald-400 font-mono text-xs p-3 rounded-lg overflow-x-auto">
                                        php tunnel-client.php --port={selectedTunnel.target_port}
                                    </div>
                                </div>
                            </div>
                            <div className="flex items-start gap-3">
                                <span className="bg-[#7c3aed] text-white rounded-full w-6 h-6 flex items-center justify-center text-xs font-bold shrink-0 mt-0.5">3</span>
                                <div>
                                    <p className="font-bold text-[#333] dark:text-white mb-1">Akses URL</p>
                                    <a href={`https://${selectedTunnel.subdomain}.ryaze.my.id`} target="_blank" rel="noopener noreferrer"
                                        className="text-[#7c3aed] dark:text-[#a78bfa] hover:underline font-mono text-sm">
                                        https://{selectedTunnel.subdomain}.ryaze.my.id
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            )}

            {/* Documentation Modal */}
            {showDocsModal && (
                <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
                    <div className="w-full max-w-2xl max-h-[85vh] bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-xl shadow-xl flex flex-col">
                        <div className="p-5 border-b border-[#e5e5e5] dark:border-[#1a1a2e] flex items-center justify-between shrink-0">
                            <h3 className="font-bold text-[#333] dark:text-white text-sm flex items-center gap-2">
                                <i className="fa-solid fa-book text-[#7c3aed]"></i> Dokumentasi Tunnel
                            </h3>
                            <button onClick={() => setShowDocsModal(false)} className="text-[#999] hover:text-red-500 transition">
                                <i className="fa-solid fa-xmark"></i>
                            </button>
                        </div>
                        <div className="p-5 overflow-y-auto flex-1 space-y-6 text-sm text-[#666] dark:text-white/60">
                            <section>
                                <h4 className="font-bold text-[#333] dark:text-white mb-2">Apa itu Tunnel?</h4>
                                <p>Tunnel memungkinkan Anda mengakses server lokal (localhost) dari internet publik. Cocok untuk development dan testing aplikasi yang berjalan di komputer Anda.</p>
                            </section>
                            <section>
                                <h4 className="font-bold text-[#333] dark:text-white mb-2">Perbandingan</h4>
                                <div className="grid grid-cols-2 gap-3">
                                    <div className="p-3 border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-lg">
                                        <p className="font-bold text-[#333] dark:text-white text-xs mb-1">Tunnel (Ryaze)</p>
                                        <ul className="text-xs space-y-1">
                                            <li><i className="fa-solid fa-check text-emerald-500 mr-1"></i> Gratis & instan</li>
                                            <li><i className="fa-solid fa-check text-emerald-500 mr-1"></i> SSL otomatis</li>
                                            <li><i className="fa-solid fa-check text-emerald-500 mr-1"></i> Subdomain tetap</li>
                                        </ul>
                                    </div>
                                    <div className="p-3 border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-lg">
                                        <p className="font-bold text-[#333] dark:text-white text-xs mb-1">Alternatif (ngrok)</p>
                                        <ul className="text-xs space-y-1">
                                            <li><i className="fa-solid fa-xmark text-rose-500 mr-1"></i> URL berubah</li>
                                            <li><i className="fa-solid fa-xmark text-rose-500 mr-1"></i> Rate limit</li>
                                            <li><i className="fa-solid fa-xmark text-rose-500 mr-1"></i> Fitur terbatas</li>
                                        </ul>
                                    </div>
                                </div>
                            </section>
                            <section>
                                <h4 className="font-bold text-[#333] dark:text-white mb-2">Cara Kerja</h4>
                                <ol className="list-decimal list-inside space-y-1 text-xs">
                                    <li>Client menghubungkan local port ke server Ryaze.</li>
                                    <li>Traffic dialihkan melalui koneksi aman (SSL).</li>
                                    <li>Request dari internet diteruskan ke local server Anda.</li>
                                </ol>
                            </section>
                            <section>
                                <h4 className="font-bold text-[#333] dark:text-white mb-2">Keterbatasan</h4>
                                <ul className="list-disc list-inside space-y-1 text-xs">
                                    <li>Maksimal 1 tunnel per akun (saat ini).</li>
                                    <li>Tidak untuk production traffic tinggi.</li>
                                    <li>Client harus tetap berjalan selama tunnel aktif.</li>
                                </ul>
                            </section>
                        </div>
                    </div>
                </div>
            )}
        </DashboardLayout>
    );
}
