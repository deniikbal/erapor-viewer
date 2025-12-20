<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generate PDF Laporan Hasil Belajar</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
        }
        .loading {
            display: none;
            margin: 20px 0;
        }
        .spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #27ae60;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 2s linear infinite;
            margin: 0 auto;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .btn {
            background: #3498db;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            margin: 10px;
        }
        .btn:hover {
            background: #2980b9;
        }
        .btn-success {
            background: #27ae60;
        }
        .btn-success:hover {
            background: #229954;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Generate PDF Laporan Hasil Belajar</h2>
        <p>Klik tombol di bawah untuk mengunduh PDF laporan hasil belajar siswa</p>
        
        <div class="loading" id="loading">
            <div class="spinner"></div>
            <p>Sedang membuat PDF...</p>
        </div>
        
        @if($siswaId ?? null)
            <button class="btn btn-success" onclick="generateSinglePDF()">
                📄 Download PDF Laporan Siswa
            </button>
        @else
            <button class="btn btn-success" onclick="generateAllPDF()">
                📄 Download PDF Semua Siswa
            </button>
        @endif
        
        <br><br>
        <a href="{{ url()->previous() }}" class="btn">← Kembali</a>
        
        <!-- PDF Preview Container -->
        <div id="pdfPreview" style="border: 1px solid #ddd; margin-top: 20px; min-height: 400px; background: white; display: none;">
            <!-- PDF akan ditampilkan di sini -->
        </div>
    </div>

    <script>
        const { jsPDF } = window.jspdf;
        const siswaId = @json($siswaId ?? null);
        let currentPdfDoc = null;
        
        function formatDate(dateString) {
            if (!dateString) return '';
            const date = new Date(dateString);
            const day = date.getDate();
            const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 
                           'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            const month = months[date.getMonth()];
            const year = date.getFullYear();
            return `${day} ${month} ${year}`;
        }

        // Font loading functions
        async function loadDejaVuFonts(doc) {
            if (window.fontsLoaded) return true;
            if (window.fontLoadPromise) return window.fontLoadPromise;
            
            window.fontLoadPromise = (async () => {
                try {
                    const fontPaths = {
                        'DejaVuSansCondensed-normal': '/fonts/dejavu-sans/DejaVuSansCondensed.ttf',
                        'DejaVuSansCondensed-bold': '/fonts/dejavu-sans/DejaVuSansCondensed-Bold.ttf'
                    };
                    
                    for (const [fontKey, fontPath] of Object.entries(fontPaths)) {
                        try {
                            const response = await fetch(fontPath);
                            if (response.ok) {
                                const fontArrayBuffer = await response.arrayBuffer();
                                const fontBase64 = arrayBufferToBase64(fontArrayBuffer);
                                
                                const [fontName, fontStyle] = fontKey.split('-');
                                doc.addFileToVFS(`${fontName}.ttf`, fontBase64);
                                doc.addFont(`${fontName}.ttf`, fontName, fontStyle);
                            }
                        } catch (error) {console.warn(`Error loading font ${fontKey}`, error);}
                    }
                    window.fontsLoaded = true;
                    return true;
                } catch (error) {
                    console.error('Error loading fonts:', error);
                    return false;
                }
            })();
            return window.fontLoadPromise;
        }

        function arrayBufferToBase64(buffer) {
            let binary = '';
            const bytes = new Uint8Array(buffer);
            const len = bytes.byteLength;
            for (let i = 0; i < len; i++) {
                binary += String.fromCharCode(bytes[i]);
            }
            return btoa(binary);
        }

        async function setDejaVuFont(doc, style = 'normal') {
            try {
                const loaded = await loadDejaVuFonts(doc);
                if (loaded && window.fontsLoaded) {
                    doc.setFont('DejaVuSansCondensed', style);
                } else {
                    doc.setFont('helvetica', style);
                }
            } catch (error) {
                doc.setFont('helvetica', style);
            }
        }

        async function generateLaporanHasilBelajar(student, sekolahData, kelasData, semesterData) {
            const doc = new jsPDF();
            const pageWidth = doc.internal.pageSize.getWidth();
            let yPos = 20;

            // Load DejaVu Sans font
            await loadDejaVuFonts(doc);
            await setDejaVuFont(doc, 'normal');
            doc.setFontSize(10);

            // Header Information
            const leftCol = 20;
            const colonCol = 55;
            const valueCol = 60;
            const rightLabelCol = 130;
            const rightColonCol = 160;
            const rightValueCol = 165;

            // Left side data
            doc.text('Nama Murid', leftCol, yPos);
            doc.text(':', colonCol, yPos);
            doc.text(student.full_name || '', valueCol, yPos);
            
            doc.text('Kelas', rightLabelCol, yPos);
            doc.text(':', rightColonCol, yPos);
            doc.text(kelasData?.nama || 'X MERDEKA 1', rightValueCol, yPos);
            yPos += 5;

            doc.text('NIS/NISN', leftCol, yPos);
            doc.text(':', colonCol, yPos);
            doc.text(`${student.nis || ''} / ${student.nisn || ''}`, valueCol, yPos);
            
            doc.text('Fase', rightLabelCol, yPos);
            doc.text(':', rightColonCol, yPos);
            doc.text('E', rightValueCol, yPos);
            yPos += 5;

            doc.text('Sekolah', leftCol, yPos);
            doc.text(':', colonCol, yPos);
            doc.text(sekolahData?.nama || 'SMAN 1 BANTARUJEG', valueCol, yPos);
            
            doc.text('Semester', rightLabelCol, yPos);
            doc.text(':', rightColonCol, yPos);
            doc.text(semesterData?.semester?.toString() || '1', rightValueCol, yPos);
            yPos += 5;

            doc.text('Alamat', leftCol, yPos);
            doc.text(':', colonCol, yPos);
            doc.text(sekolahData?.alamat || 'Jl. Siliwangi No. 119 Bantarujeg', valueCol, yPos);
            
            doc.text('Tahun Ajaran', rightLabelCol, yPos);
            doc.text(':', rightColonCol, yPos);
            // Extract year from nama_semester (e.g., "2025/2026 Ganjil" -> "2025/2026")
            let tahunAjaran = '2025/2026';
            if (semesterData?.nama_semester) {
                const match = semesterData.nama_semester.match(/(\d{4}\/\d{4})/);
                if (match) tahunAjaran = match[1];
            }
            doc.text(tahunAjaran, rightValueCol, yPos);
            yPos += 10;

            // Horizontal line
            doc.line(leftCol, yPos, pageWidth - 20, yPos);
            yPos += 10;

            // Title
            await setDejaVuFont(doc, 'bold');
            doc.setFontSize(14);
            doc.text('LAPORAN HASIL BELAJAR', pageWidth / 2, yPos, { align: 'center' });
            yPos += 15;

            // Content placeholder
            await setDejaVuFont(doc, 'normal');
            doc.setFontSize(10);
            doc.text('Mata Pelajaran dan Nilai akan ditampilkan di sini', leftCol, yPos);

            const fileName = `Laporan_Hasil_Belajar_${student.full_name?.replace(/\s+/g, '_') || 'Siswa'}.pdf`;
            doc.save(fileName);
        }
        
        async function generateSinglePDF() {
            if (!siswaId) { alert('ID Siswa tidak ditemukan'); return; }
            document.getElementById('loading').style.display = 'block';
            
            try {
                const response = await fetch(`/guru/siswa/${siswaId}/data`);
                const data = await response.json();
                
                if (data.error) { alert(data.error); return; }
                
                await generateLaporanHasilBelajar(data.siswa, data.sekolah, data.kelas, data.semester);
                alert('PDF berhasil diunduh');
                
            } catch (error) {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat membuat PDF: ' + error.message);
            } finally {
                document.getElementById('loading').style.display = 'none';
            }
        }
        
        async function generateAllPDF() {
            document.getElementById('loading').style.display = 'block';
            
            try {
                const response = await fetch('/guru/siswa/data/all');
                const data = await response.json();
                
                if (data.error) { alert(data.error); return; }
                if (data.siswaList.length === 0) { alert('Tidak ada data siswa'); return; }
                
                alert('Fitur download semua siswa akan segera tersedia');
                
            } catch (error) {
                console.error('Error:', error);
                alert('Terjadi kesalahan');
            } finally {
                document.getElementById('loading').style.display = 'none';
            }
        }
    </script>
</body>
</html>