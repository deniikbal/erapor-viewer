<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generate PDF Identitas Siswa</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script type="module" src="{{ asset('js/pdfFonts.js') }}"></script>
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
            border-top: 4px solid #3498db;
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
        .btn-danger {
            background: #e74c3c;
        }
        .btn-danger:hover {
            background: #c0392b;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Generate PDF Identitas Siswa</h2>
        <p>Klik tombol di bawah untuk mengunduh PDF identitas siswa</p>
        
        <div class="loading" id="loading">
            <div class="spinner"></div>
            <p>Sedang membuat PDF...</p>
        </div>
        
        @if($siswaId)
            <button class="btn btn-danger" onclick="generateSinglePDF()">
                📄 Download PDF Siswa
            </button>
        @else
            <button class="btn btn-danger" onclick="generateAllPDF()">
                📄 Download PDF Semua Siswa
            </button>
        @endif
        
        <br><br>
        <a href="{{ url()->previous() }}" class="btn">← Kembali</a>
    </div>

    <script>
        const { jsPDF } = window.jspdf;
        const siswaId = @json($siswaId);
        
        // DejaVu Sans font data (base64 encoded)
        const dejaVuSansNormal = 'data:font/truetype;charset=utf-8;base64,'; // This would be the actual font data
        const dejaVuSansBold = 'data:font/truetype;charset=utf-8;base64,'; // This would be the actual font data
        
        // Helper functions
        function capitalizeWords(str) {
            if (!str) return '';
            return str.toLowerCase().replace(/\b\w/g, l => l.toUpperCase());
        }
        
        function toUpperCase(str) {
            return str ? str.toUpperCase() : '';
        }
        
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
        
        // Import the font loading functions
        async function loadDejaVuFonts(doc) {
            if (window.fontsLoaded) return true;
            if (window.fontLoadPromise) return window.fontLoadPromise;
            
            window.fontLoadPromise = (async () => {
                try {
                    // Load DejaVu Sans Condensed fonts (which we have)
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
                                
                                // Add font to jsPDF
                                const [fontName, fontStyle] = fontKey.split('-');
                                doc.addFileToVFS(`${fontName}.ttf`, fontBase64);
                                doc.addFont(`${fontName}.ttf`, fontName, fontStyle);
                                
                                console.log(`✅ Loaded font: ${fontKey}`);
                            } else {
                                console.warn(`⚠️ Font not found: ${fontPath}`);
                            }
                        } catch (error) {
                            console.warn(`⚠️ Error loading font ${fontKey}:`, error);
                        }
                    }
                    
                    window.fontsLoaded = true;
                    return true;
                } catch (error) {
                    console.error('❌ Error loading DejaVu fonts:', error);
                    return false;
                }
            })();
            
            return window.fontLoadPromise;
        }

        // Helper function to convert ArrayBuffer to base64
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
                // Try to load fonts first
                const loaded = await loadDejaVuFonts(doc);
                
                if (loaded && window.fontsLoaded) {
                    // Use DejaVu Sans Condensed (which we have)
                    doc.setFont('DejaVuSansCondensed', style);
                    return true;
                } else {
                    // Fallback to Helvetica
                    console.warn('⚠️ DejaVu fonts not available, using Helvetica fallback');
                    doc.setFont('helvetica', style);
                    return false;
                }
            } catch (error) {
                console.warn('⚠️ Font setting error, using Helvetica fallback:', error);
                doc.setFont('helvetica', style);
                return false;
            }
        }
        
        async function generatePDFIdentitas(student) {
            const doc = new jsPDF();
            const pageWidth = doc.internal.pageSize.getWidth();
            const pageHeight = doc.internal.pageSize.getHeight();
            const marginLeft = 20;
            const marginRight = 20;
            const marginTop = 15;
            const marginBottom = 10;
            const margin = marginLeft;
            let yPos = marginTop + 5; // Further reduced for tighter top margin

            // Load image pp.jpg
            let photoBase64 = '';
            try {
                const response = await fetch('/img/pp.jpg');
                const blob = await response.blob();
                photoBase64 = await new Promise((resolve) => {
                    const reader = new FileReader();
                    reader.onloadend = () => resolve(reader.result);
                    reader.readAsDataURL(blob);
                });
            } catch (error) {
                console.error('Error loading photo:', error);
            }

            // Title
            doc.setFontSize(14);
            doc.setFont('helvetica', 'bold');
            doc.text('IDENTITAS PESERTA DIDIK', pageWidth / 2, yPos, { align: 'center' });
            
            yPos += 13; // Reduced from 12 to 10 for tighter spacing
            doc.setFontSize(11);
            
            // Use DejaVu Sans Condensed for student data
            await setDejaVuFont(doc, 'normal');

            // Helper function to add rows
            const addRow = (no, label, value, isSubItem = false) => {
                const xNo = margin;
                const xLabel = margin + 10;
                const xColon = margin + 70;
                const xValue = margin + 75;
                
                // Number
                if (no) {
                    doc.text(no, xNo, yPos);
                }
                
                // Label
                doc.text(label, xLabel, yPos);
                
                // Colon
                doc.text(':', xColon, yPos);
                
                // Value with word wrap
                if (value) {
                    const maxWidth = pageWidth - xValue - margin;
                    const lines = doc.splitTextToSize(value, maxWidth);
                    lines.forEach((line, i) => {
                        doc.text(line, xValue, yPos + (i * 5));
                    });
                    yPos += (lines.length - 1) * 5 + 7;
                } else {
                    yPos += 7;
                }
            };

            // Student data
            addRow('1.', 'Nama Lengkap Peserta Didik', student.full_name || '');
            addRow('2.', 'Nomor Induk/NISN', `${student.nis || ''} / ${student.nisn || ''}`);
            
            // Handle birth date
            let birthDateFormatted = '';
            if (student.birth_date) {
                if (student.birth_date.match(/^\d{4}-\d{2}-\d{2}$/)) {
                    birthDateFormatted = formatDate(student.birth_date);
                } else {
                    birthDateFormatted = student.birth_date;
                }
            }
            
            const birthInfo = student.birth_place && birthDateFormatted 
                ? `${student.birth_place}, ${birthDateFormatted}`
                : '';
            addRow('3.', 'Tempat ,Tanggal Lahir', birthInfo);
            
            addRow('4.', 'Jenis Kelamin', student.gender || '');
            addRow('5.', 'Agama', student.religion || '');
            addRow('6.', 'Status dalam Keluarga', student.family_status || '');
            addRow('7.', 'Anak ke', student.child_number ? String(student.child_number) : '');
            addRow('8.', 'Alamat Peserta Didik', student.address || '');
            addRow('9.', 'Nomor Telepon Rumah', student.phone_number || '');
            addRow('10.', 'Sekolah Asal', toUpperCase(student.previous_school));
            
            addRow('11.', 'Diterima di sekolah ini', '');
            addRow('', 'Di kelas', student.accepted_class || 'X');
            addRow('', 'Pada tanggal', '14 Juli 2025');
            
            addRow('12.', 'Nama Orang Tua', '');
            addRow('', 'a. Ayah', capitalizeWords(student.father_name), true);
            addRow('', 'b. Ibu', capitalizeWords(student.mother_name), true);
            
            addRow('13.', 'Alamat Orang Tua', student.parent_address || '');
            addRow('', 'Nomor Telepon Rumah', student.parent_phone || '', true);
            
            addRow('14.', 'Pekerjaan Orang Tua :', '');
            addRow('', 'a. Ayah', student.father_job || '', true);
            addRow('', 'b. Ibu', student.mother_job || '', true);
            
            addRow('15.', 'Nama Wali Siswa', student.guardian_name || '');
            addRow('16.', 'Alamat Wali Peserta Didik', student.guardian_address || '');
            addRow('', 'Nomor Telepon Rumah', student.guardian_phone || '', true);
            addRow('17.', 'Pekerjaan Wali Peserta Didik', student.guardian_job || '');

            // Signature section
            yPos += 13;
            const signatureStartY = yPos;
            const photoX = margin + 47;
            const signatureX = pageWidth - 100;
            
            // Add photo if available
            if (photoBase64) {
                const photoWidth = 30;
                const photoHeight = 40;
                doc.addImage(photoBase64, 'JPEG', photoX, signatureStartY, photoWidth, photoHeight);
            } else {
                // Fallback: Photo placeholder
                doc.rect(photoX, signatureStartY, 30, 40);
                doc.setFontSize(8);
                doc.text('Foto 3x4', photoX + 15, signatureStartY + 22, { align: 'center' });
            }
            
            // Reset yPos for signature
            yPos = signatureStartY + 4;
            
            // Date and position
            await setDejaVuFont(doc, 'normal');
            doc.setFontSize(11);
            doc.text('Bantarujeg, 14 Juli 2025', signatureX, yPos);
            yPos += 5;
            doc.text('Kepala Sekolah', signatureX, yPos);
            
            // Name with bold and underline
            yPos += 24;
            await setDejaVuFont(doc, 'bold');
            doc.setFontSize(10);
            const namaKepala = 'Dr. H. Toto Warsito, S.Ag., M.Ag.';
            doc.text(namaKepala, signatureX, yPos);
            
            // Add underline
            try {
                const textWidth = doc.getTextWidth(namaKepala);
                doc.line(signatureX, yPos + 1, signatureX + textWidth, yPos + 1);
            } catch (error) {
                console.warn('Error getting text width, using fallback underline:', error);
                const estimatedWidth = namaKepala.length * 2.5;
                doc.line(signatureX, yPos + 1, signatureX + estimatedWidth, yPos + 1);
            }
            
            // NIP
            yPos += 5;
            await setDejaVuFont(doc, 'bold');
            doc.setFontSize(10);
            doc.text('NIP. 19730302 199802 1 002', signatureX, yPos);

            // Save PDF
            const fileName = `Identitas_${student.full_name?.replace(/\s+/g, '_') || 'Siswa'}.pdf`;
            doc.save(fileName);
        }
        
        async function generateSinglePDF() {
            if (!siswaId) {
                alert('ID Siswa tidak ditemukan');
                return;
            }
            
            document.getElementById('loading').style.display = 'block';
            
            try {
                const response = await fetch(`/guru/siswa/${siswaId}/data`);
                const data = await response.json();
                
                if (data.error) {
                    alert(data.error);
                    return;
                }
                
                await generatePDFIdentitas(data.siswa);
                alert('PDF berhasil diunduh');
                
            } catch (error) {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat membuat PDF');
            } finally {
                document.getElementById('loading').style.display = 'none';
            }
        }
        
        async function generateAllPDF() {
            document.getElementById('loading').style.display = 'block';
            
            try {
                const response = await fetch('/guru/siswa/data/all');
                const data = await response.json();
                
                if (data.error) {
                    alert(data.error);
                    return;
                }
                
                if (data.siswaList.length === 0) {
                    alert('Tidak ada data siswa untuk dicetak');
                    return;
                }
                
                const doc = new jsPDF();
                const pageWidth = doc.internal.pageSize.getWidth();
                const pageHeight = doc.internal.pageSize.getHeight();
                
                // Load photo once for all pages
                let photoBase64 = '';
                try {
                    const response = await fetch('/img/pp.jpg');
                    const blob = await response.blob();
                    photoBase64 = await new Promise((resolve) => {
                        const reader = new FileReader();
                        reader.onloadend = () => resolve(reader.result);
                        reader.readAsDataURL(blob);
                    });
                } catch (error) {
                    console.error('Error loading photo:', error);
                }
                
                for (let i = 0; i < data.siswaList.length; i++) {
                    if (i > 0) {
                        doc.addPage();
                    }
                    
                    // Generate each student's page
                    await generateStudentPage(doc, data.siswaList[i], i + 1, photoBase64);
                }
                
                const fileName = `Identitas_Semua_Siswa_${data.kelas?.nama?.replace(/\s+/g, '_') || 'Kelas'}.pdf`;
                doc.save(fileName);
                alert(`PDF dengan ${data.siswaList.length} siswa berhasil dibuat`);
                
            } catch (error) {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat membuat PDF');
            } finally {
                document.getElementById('loading').style.display = 'none';
            }
        }
        
        async function generateStudentPage(doc, student, pageNumber, photoBase64 = '') {
            // Similar to generatePDFIdentitas but for existing doc
            const pageWidth = doc.internal.pageSize.getWidth();
            const marginLeft = 20;
            const margin = marginLeft;
            let yPos = 25; // Reduced top margin for consistency

            // Title
            doc.setFontSize(14);
            doc.setFont('helvetica', 'bold');
            doc.text('IDENTITAS PESERTA DIDIK', pageWidth / 2, yPos, { align: 'center' });
            
            yPos += 6;
            doc.setFontSize(12);
            doc.text(`${pageNumber}. ${student.full_name}`, pageWidth / 2, yPos, { align: 'center' });
            
            yPos += 10;
            doc.setFontSize(11);
            await setDejaVuFont(doc, 'normal');

            // Add all the same rows as in generatePDFIdentitas
            // (Implementation would be the same as above)
            
            // For brevity, I'll add just a few key fields
            const addRow = (no, label, value) => {
                const xNo = margin;
                const xLabel = margin + 10;
                const xColon = margin + 70;
                const xValue = margin + 75;
                
                if (no) doc.text(no, xNo, yPos);
                doc.text(label, xLabel, yPos);
                doc.text(':', xColon, yPos);
                if (value) doc.text(value, xValue, yPos);
                yPos += 7;
            };

            // Student data (same as generatePDFIdentitas)
            addRow('1.', 'Nama Lengkap Peserta Didik', student.full_name || '');
            addRow('2.', 'Nomor Induk/NISN', `${student.nis || ''} / ${student.nisn || ''}`);
            
            // Handle birth date
            let birthDateFormatted = '';
            if (student.birth_date) {
                if (student.birth_date.match(/^\d{4}-\d{2}-\d{2}$/)) {
                    birthDateFormatted = formatDate(student.birth_date);
                } else {
                    birthDateFormatted = student.birth_date;
                }
            }
            
            const birthInfo = student.birth_place && birthDateFormatted 
                ? `${student.birth_place}, ${birthDateFormatted}`
                : '';
            addRow('3.', 'Tempat ,Tanggal Lahir', birthInfo);
            
            addRow('4.', 'Jenis Kelamin', student.gender || '');
            addRow('5.', 'Agama', student.religion || '');
            addRow('6.', 'Status dalam Keluarga', student.family_status || '');
            addRow('7.', 'Anak ke', student.child_number ? String(student.child_number) : '');
            addRow('8.', 'Alamat Peserta Didik', student.address || '');
            addRow('9.', 'Nomor Telepon Rumah', student.phone_number || '');
            addRow('10.', 'Sekolah Asal', toUpperCase(student.previous_school));
            
            addRow('11.', 'Diterima di sekolah ini', '');
            addRow('', 'Di kelas', 'X');
            addRow('', 'Pada tanggal', '14 Juli 2025');
            
            addRow('12.', 'Nama Orang Tua', '');
            addRow('', 'a. Ayah', capitalizeWords(student.father_name));
            addRow('', 'b. Ibu', capitalizeWords(student.mother_name));
            
            addRow('13.', 'Alamat Orang Tua', student.parent_address || '');
            addRow('', 'Nomor Telepon Rumah', student.parent_phone || '');
            
            addRow('14.', 'Pekerjaan Orang Tua :', '');
            addRow('', 'a. Ayah', student.father_job || '');
            addRow('', 'b. Ibu', student.mother_job || '');
            
            addRow('15.', 'Nama Wali Siswa', student.guardian_name || '');
            addRow('16.', 'Alamat Wali Peserta Didik', student.guardian_address || '');
            addRow('', 'Nomor Telepon Rumah', student.guardian_phone || '');
            addRow('17.', 'Pekerjaan Wali Peserta Didik', student.guardian_job || '');

            // Signature section
            yPos += 13;
            const signatureStartY = yPos;
            const photoX = margin + 47;
            const signatureX = doc.internal.pageSize.getWidth() - 100;
            
            // Add photo if available
            if (photoBase64) {
                const photoWidth = 30;
                const photoHeight = 40;
                doc.addImage(photoBase64, 'JPEG', photoX, signatureStartY, photoWidth, photoHeight);
            } else {
                // Fallback: Photo placeholder
                doc.rect(photoX, signatureStartY, 30, 40);
                doc.setFontSize(8);
                doc.text('Foto 3x4', photoX + 15, signatureStartY + 22, { align: 'center' });
            }
            
            // Reset yPos for signature
            yPos = signatureStartY + 4;
            
            // Date and position
            await setDejaVuFont(doc, 'normal');
            doc.setFontSize(11);
            doc.text('Bantarujeg, 14 Juli 2025', signatureX, yPos);
            yPos += 5;
            doc.text('Kepala Sekolah', signatureX, yPos);
            
            // Name with bold and underline
            yPos += 24;
            await setDejaVuFont(doc, 'bold');
            doc.setFontSize(10);
            const namaKepala = 'Dr. H. Toto Warsito, S.Ag., M.Ag.';
            doc.text(namaKepala, signatureX, yPos);
            
            // Add underline
            try {
                const textWidth = doc.getTextWidth(namaKepala);
                doc.line(signatureX, yPos + 1, signatureX + textWidth, yPos + 1);
            } catch (error) {
                console.warn('Error getting text width, using fallback underline:', error);
                const estimatedWidth = namaKepala.length * 2.5;
                doc.line(signatureX, yPos + 1, signatureX + estimatedWidth, yPos + 1);
            }
            
            // NIP
            yPos += 5;
            await setDejaVuFont(doc, 'bold');
            doc.setFontSize(10);
            doc.text('NIP. 19730302 199802 1 002', signatureX, yPos);
        }
        
        // Auto-generate if siswaId is provided
        if (siswaId) {
            // Auto-generate after page load
            window.addEventListener('load', () => {
                setTimeout(generateSinglePDF, 1000);
            });
        }
    </script>
</body>
</html>