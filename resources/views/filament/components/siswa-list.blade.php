<style>
    .siswa-table {
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        width: 100%;
    }
    .table-header {
        background: #f8fafc;
        padding: 15px;
        border-bottom: 2px solid #e2e8f0;
        text-align: center;
    }
    .table-title {
        font-size: 16px;
        font-weight: bold;
        color: #1f2937;
        margin: 0 0 5px 0;
    }
    .table-subtitle {
        font-size: 14px;
        color: #6b7280;
        margin: 0;
    }
    .data-table {
        width: 100%;
        border-collapse: collapse;
        margin: 0;
    }
    .data-table th {
        background: #f1f5f9;
        padding: 12px 8px;
        text-align: left;
        font-weight: 600;
        font-size: 14px;
        color: #374151;
        border-bottom: 2px solid #d1d5db;
    }
    .data-table th:first-child {
        text-align: center;
        width: 50px;
    }
    .data-table td {
        padding: 10px 8px;
        border-bottom: 1px solid #e5e7eb;
        font-size: 13px;
        color: #374151;
    }
    .data-table td:first-child {
        text-align: center;
        font-weight: 600;
        color: #6b7280;
    }
    .data-table tr:hover {
        background: #f9fafb;
    }
    .data-table tr:nth-child(even) {
        background: #fafbfc;
    }
    .data-table tr:nth-child(even):hover {
        background: #f3f4f6;
    }
    .table-container {
        max-height: 400px;
        overflow-y: auto;
        border: 1px solid #e5e7eb;
    }
    .empty-state {
        text-align: center;
        padding: 40px 20px;
        color: #6b7280;
    }
    .total-info {
        background: #eff6ff;
        padding: 10px 15px;
        border-top: 1px solid #e5e7eb;
        font-size: 14px;
        color: #1e40af;
        font-weight: 500;
    }
</style>

<div class="siswa-table">
    <!-- Header -->
    <div class="table-header">
        <h3 class="table-title">👥 Daftar Siswa</h3>
        <p class="table-subtitle">Total: {{ $total }} siswa</p>
    </div>
    
    @if($siswa->count() > 0)
        <!-- Table Container -->
        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Lengkap</th>
                        <th>NIS</th>
                        <th>NISN</th>
                        <th>Jenis Kelamin</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($siswa as $index => $s)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $s['nama'] }}</td>
                            <td>{{ $s['nis'] }}</td>
                            <td>{{ $s['nisn'] }}</td>
                            <td>{{ $s['jenis_kelamin'] == 'L' ? 'Laki-laki' : ($s['jenis_kelamin'] == 'P' ? 'Perempuan' : $s['jenis_kelamin']) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <!-- Total Info -->
        <div class="total-info">
            📊 Total {{ $total }} siswa | 
            NIS Lengkap: {{ $siswa->where('nis', '!=', 'N/A')->count() }} | 
            NISN Lengkap: {{ $siswa->where('nisn', '!=', 'N/A')->count() }}
        </div>
    @else
        <!-- Empty State -->
        <div class="empty-state">
            <p><strong>Belum Ada Siswa</strong></p>
            <p>Kelas ini belum memiliki anggota siswa yang terdaftar.</p>
        </div>
    @endif
</div>