import React, { useEffect, useState, useMemo } from 'react';
import { supabase } from '../lib/supabase';
import { Users, User, UserRound, GraduationCap, Filter, Search, Edit, Trash2, Eye, UserPlus, Calendar, BarChart3, CheckCircle, Activity, FileText, XCircle, Percent, Check, AlertCircle, Clock, X, Plus, Download } from 'lucide-react';
import { Modal } from '../components/Common/Modal';
import { Pagination } from '../components/Common/Pagination';
import toast from 'react-hot-toast';
import { Link } from 'react-router-dom';
import { useAuth } from '../contexts/AuthContext';
import jsPDF from 'jspdf';
import { setDejaVuFont } from '../utils/pdfFonts';
// import { Tabs } from '@headlessui/react';

interface Student {
  id: string;
  full_name: string;
  nis?: string;
  nisn?: string;
  birth_place?: string;
  birth_date?: string;
  gender?: string;
  religion?: string;
  family_status?: string;
  child_number?: number;
  address?: string;
  phone_number?: string;
  previous_school?: string;
  father_name?: string;
  mother_name?: string;
  parent_address?: string;
  father_job?: string;
  mother_job?: string;
  parent_whatsapp?: string;
  catatan?: string; // Tambah field catatan
  class_id?: string; // Tambah field class_id
  created_at?: string;
  updated_at?: string;
  verified?: boolean; // Status verifikasi data siswa
}

// Utility function untuk format waktu relatif dalam bahasa Indonesia
function getRelativeTime(timestamp: string | undefined): string {
  if (!timestamp) return 'Tidak ada data';
  
  const now = new Date();
  const past = new Date(timestamp);
  const diffMs = now.getTime() - past.getTime();
  const diffSeconds = Math.floor(diffMs / 1000);
  const diffMinutes = Math.floor(diffSeconds / 60);
  const diffHours = Math.floor(diffMinutes / 60);
  const diffDays = Math.floor(diffHours / 24);
  const diffMonths = Math.floor(diffDays / 30);
  const diffYears = Math.floor(diffDays / 365);

  if (diffSeconds < 60) {
    return 'Baru saja';
  } else if (diffMinutes < 60) {
    return `${diffMinutes} menit lalu`;
  } else if (diffHours < 24) {
    return `${diffHours} jam lalu`;
  } else if (diffDays < 30) {
    return `${diffDays} hari lalu`;
  } else if (diffMonths < 12) {
    return `${diffMonths} bulan lalu`;
  } else {
    return `${diffYears} tahun lalu`;
  }
}

function StudentDetailModal({ student, onClose }: { student: Student | null; onClose: () => void }) {
  if (!student) return null;
  return (
    <Modal isOpen={!!student} onClose={onClose} title="Detail Biodata Siswa" size="xl">
      <div className="overflow-x-auto">
        <div className="grid grid-cols-1 md:grid-cols-2 gap-x-8">
          <table className="min-w-full text-sm mb-2">
            <tbody>
              <tr>
                <td className="font-semibold text-gray-700 py-1 pr-4 whitespace-nowrap">Nama Lengkap Peserta Didik</td>
                <td className="text-gray-700 text-center align-top py-1 pr-2" style={{width: '16px'}}>: </td>
                <td className="text-gray-800 py-1">{student.full_name}</td>
              </tr>
              <tr>
                <td className="font-semibold text-gray-700 py-1 pr-4 whitespace-nowrap">Nomor Induk/NISN</td>
                <td className="text-gray-700 text-center align-top py-1 pr-2">: </td>
                <td className="text-gray-800 py-1">{student.nis}{student.nis && student.nisn ? ' / ' : ''}{student.nisn}</td>
              </tr>
              <tr>
                <td className="font-semibold text-gray-700 py-1 pr-4 whitespace-nowrap">Tempat, Tanggal Lahir</td>
                <td className="text-gray-700 text-center align-top py-1 pr-2">: </td>
                <td className="text-gray-800 py-1">{student.birth_place}{student.birth_place && student.birth_date ? ', ' : ''}{student.birth_date}</td>
              </tr>
              <tr>
                <td className="font-semibold text-gray-700 py-1 pr-4 whitespace-nowrap">Jenis Kelamin</td>
                <td className="text-gray-700 text-center align-top py-1 pr-2">: </td>
                <td className="text-gray-800 py-1">{student.gender}</td>
              </tr>
              <tr>
                <td className="font-semibold text-gray-700 py-1 pr-4 whitespace-nowrap">Agama</td>
                <td className="text-gray-700 text-center align-top py-1 pr-2">: </td>
                <td className="text-gray-800 py-1">{student.religion}</td>
              </tr>
              <tr>
                <td className="font-semibold text-gray-700 py-1 pr-4 whitespace-nowrap">Status dalam Keluarga</td>
                <td className="text-gray-700 text-center align-top py-1 pr-2">: </td>
                <td className="text-gray-800 py-1">{student.family_status}</td>
              </tr>
              <tr>
                <td className="font-semibold text-gray-700 py-1 pr-4 whitespace-nowrap">Anak Ke</td>
                <td className="text-gray-700 text-center align-top py-1 pr-2">: </td>
                <td className="text-gray-800 py-1">{student.child_number}</td>
              </tr>
              <tr>
                <td className="font-semibold text-gray-700 py-1 pr-4 whitespace-nowrap">Alamat</td>
                <td className="text-gray-700 text-center align-top py-1 pr-2">: </td>
                <td className="text-gray-800 py-1 capitalize">{student.address}</td>
              </tr>
            </tbody>
          </table>
          <table className="min-w-full text-sm mb-2">
            <tbody>
              <tr>
                <td className="font-semibold text-gray-700 py-1 pr-4 whitespace-nowrap">No Handphone</td>
                <td className="text-gray-700 text-center align-top py-1 pr-2">: </td>
                <td className="text-gray-800 py-1">{student.phone_number}</td>
              </tr>
              <tr>
                <td className="font-semibold text-gray-700 py-1 pr-4 whitespace-nowrap">Asal Sekolah</td>
                <td className="text-gray-700 text-center align-top py-1 pr-2">: </td>
                <td className="text-gray-800 py-1">{student.previous_school}</td>
              </tr>
              <tr>
                <td className="font-semibold text-gray-700 py-1 pr-4 whitespace-nowrap">Nama Ayah</td>
                <td className="text-gray-700 text-center align-top py-1 pr-2">: </td>
                <td className="text-gray-800 py-1">{student.father_name}</td>
              </tr>
              <tr>
                <td className="font-semibold text-gray-700 py-1 pr-4 whitespace-nowrap">Nama Ibu</td>
                <td className="text-gray-700 text-center align-top py-1 pr-2">: </td>
                <td className="text-gray-800 py-1">{student.mother_name}</td>
              </tr>
              <tr>
                <td className="font-semibold text-gray-700 py-1 pr-4 whitespace-nowrap">Alamat Orang Tua</td>
                <td className="text-gray-700 text-center align-top py-1 pr-2">: </td>
                <td className="text-gray-800 py-1 capitalize">{student.parent_address}</td>
              </tr>
              <tr>
                <td className="font-semibold text-gray-700 py-1 pr-4 whitespace-nowrap">Pekerjaan Ayah</td>
                <td className="text-gray-700 text-center align-top py-1 pr-2">: </td>
                <td className="text-gray-800 py-1">{student.father_job}</td>
              </tr>
              <tr>
                <td className="font-semibold text-gray-700 py-1 pr-4 whitespace-nowrap">Pekerjaan Ibu</td>
                <td className="text-gray-700 text-center align-top py-1 pr-2">: </td>
                <td className="text-gray-800 py-1">{student.mother_job}</td>
              </tr>
              <tr>
                <td className="font-semibold text-gray-700 py-1 pr-4 whitespace-nowrap">No WA Ayah / Ibu</td>
                <td className="text-gray-700 text-center align-top py-1 pr-2">: </td>
                <td className="text-gray-800 py-1">{student.parent_whatsapp}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </Modal>
  );
}

function StudentEditModal({ student, open, onClose, onSave }: { student: Student | null, open: boolean, onClose: () => void, onSave: (data: Student) => void }) {
  const [form, setForm] = React.useState<Student | null>(student);
  const [saving, setSaving] = React.useState(false);
  React.useEffect(() => { setForm(student); }, [student]);
  if (!open || !form) return null;
  const handleChange = (e: React.ChangeEvent<HTMLInputElement | HTMLSelectElement | HTMLTextAreaElement>) => {
    const { name, value, type } = e.target;
    if (type === 'checkbox') {
      const checked = (e.target as HTMLInputElement).checked;
      setForm({ ...form, [name]: checked });
    } else {
      setForm({ ...form, [name]: value });
    }
  };
  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setSaving(true);
    await onSave(form as Student);
    setSaving(false);
  };
  return (
    <Modal isOpen={open} onClose={onClose} title="Edit Biodata Siswa" size="xl">
      <form onSubmit={handleSubmit} className="grid grid-cols-1 md:grid-cols-3 gap-x-6 gap-y-2 text-sm">
        <div><label className="font-semibold mb-1 block text-gray-700">Nama Lengkap</label><input name="full_name" value={form.full_name || ''} onChange={handleChange} className="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-400 focus:border-blue-500" required /></div>
        <div><label className="font-semibold mb-1 block text-gray-700">NIS</label><input name="nis" value={form.nis || ''} onChange={handleChange} className="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-400 focus:border-blue-500" /></div>
        <div><label className="font-semibold mb-1 block text-gray-700">NISN</label><input name="nisn" value={form.nisn || ''} onChange={handleChange} className="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-400 focus:border-blue-500" /></div>
        <div><label className="font-semibold mb-1 block text-gray-700">Tempat Lahir</label><input name="birth_place" value={form.birth_place || ''} onChange={handleChange} className="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-400 focus:border-blue-500" /></div>
        <div><label className="font-semibold mb-1 block text-gray-700">Tanggal Lahir</label><input type="date" name="birth_date" value={form.birth_date || ''} onChange={handleChange} className="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-400 focus:border-blue-500" /></div>
        <div><label className="font-semibold mb-1 block text-gray-700">Jenis Kelamin</label><select name="gender" value={form.gender || ''} onChange={handleChange} className="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-400 focus:border-blue-500"><option value="">Pilih</option><option value="Laki-laki">Laki-laki</option><option value="Perempuan">Perempuan</option></select></div>
        <div><label className="font-semibold mb-1 block text-gray-700">Agama</label><select name="religion" value={form.religion || ''} onChange={handleChange} className="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-400 focus:border-blue-500"><option value="">Pilih</option><option value="Islam">Islam</option><option value="Kristen Protestan">Kristen Protestan</option><option value="Katolik">Katolik</option><option value="Hindu">Hindu</option><option value="Budha">Budha</option><option value="Konghuchu">Konghuchu</option></select></div>
        <div><label className="font-semibold mb-1 block text-gray-700">Status Keluarga</label><input name="family_status" value={form.family_status || ''} onChange={handleChange} className="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-400 focus:border-blue-500" /></div>
        <div><label className="font-semibold mb-1 block text-gray-700">Anak Ke</label><input type="number" name="child_number" value={form.child_number || ''} onChange={handleChange} className="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-400 focus:border-blue-500" /></div>
        <div className="md:col-span-3"><label className="font-semibold mb-1 block text-gray-700">Alamat</label><input name="address" value={form.address || ''} onChange={handleChange} className="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-400 focus:border-blue-500" /></div>
        <div><label className="font-semibold mb-1 block text-gray-700">No HP</label><input name="phone_number" value={form.phone_number || ''} onChange={handleChange} className="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-400 focus:border-blue-500" /></div>
        <div><label className="font-semibold mb-1 block text-gray-700">Asal Sekolah</label><input name="previous_school" value={form.previous_school || ''} onChange={handleChange} className="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-400 focus:border-blue-500" /></div>
        <div><label className="font-semibold mb-1 block text-gray-700">Nama Ayah</label><input name="father_name" value={form.father_name || ''} onChange={handleChange} className="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-400 focus:border-blue-500" /></div>
        <div><label className="font-semibold mb-1 block text-gray-700">Nama Ibu</label><input name="mother_name" value={form.mother_name || ''} onChange={handleChange} className="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-400 focus:border-blue-500" /></div>
        <div className="md:col-span-3"><label className="font-semibold mb-1 block text-gray-700">Alamat Ortu</label><input name="parent_address" value={form.parent_address || ''} onChange={handleChange} className="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-400 focus:border-blue-500" /></div>
        <div><label className="font-semibold mb-1 block text-gray-700">Pekerjaan Ayah</label><input name="father_job" value={form.father_job || ''} onChange={handleChange} className="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-400 focus:border-blue-500" /></div>
        <div><label className="font-semibold mb-1 block text-gray-700">Pekerjaan Ibu</label><input name="mother_job" value={form.mother_job || ''} onChange={handleChange} className="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-400 focus:border-blue-500" /></div>
        <div className="md:col-span-3"><label className="font-semibold mb-1 block text-gray-700">No WA Ortu</label><input name="parent_whatsapp" value={form.parent_whatsapp || ''} onChange={handleChange} className="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-400 focus:border-blue-500" /></div>
        {/* Tambah field verified */}
        <div className="md:col-span-3">
          <label className="flex items-center gap-2 cursor-pointer">
            <input 
              type="checkbox" 
              name="verified" 
              checked={form.verified || false} 
              onChange={handleChange} 
              className="w-4 h-4 text-green-600 border-gray-300 rounded focus:ring-green-500"
            />
            <span className="font-semibold text-gray-700 flex items-center gap-1">
              <CheckCircle className="h-4 w-4 text-green-600" />
              Data Siswa Sudah Diverifikasi
            </span>
          </label>
          <p className="text-xs text-gray-500 ml-6 mt-1">Centang jika data siswa sudah dicek dan diverifikasi kebenarannya</p>
        </div>
        {/* Tambah field catatan khusus modal edit */}
        <div className="md:col-span-3">
          <label className="font-semibold mb-1 block text-gray-700">Catatan</label>
          <textarea name="catatan" value={form.catatan || ''} onChange={handleChange} rows={3} className="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-400 focus:border-blue-500" placeholder="Catatan tambahan untuk siswa ini (hanya untuk admin/guru)"></textarea>
        </div>
        <div className="md:col-span-3 flex justify-end mt-4 gap-2">
          <button type="button" onClick={onClose} className="px-5 py-2 rounded-lg bg-gray-200 text-gray-700 text-xs font-semibold hover:bg-gray-300">Batal</button>
          <button type="submit" disabled={saving} className="px-7 py-2 bg-blue-600 text-white rounded-lg font-semibold text-xs shadow hover:bg-blue-700 disabled:opacity-50 transition-all">{saving ? 'Menyimpan...' : 'Simpan'}</button>
        </div>
      </form>
    </Modal>
  );
}

function ConfirmDeleteModal({ open, onClose, onDelete }: { open: boolean, onClose: () => void, onDelete: () => void }) {
  if (!open) return null;
  return (
    <div className="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40">
      <div className="bg-white rounded-lg shadow-lg max-w-sm w-full p-4 md:p-6 relative animate-fadeIn max-h-screen overflow-y-auto">
        <button onClick={onClose} className="absolute top-2 right-2 text-gray-400 hover:text-red-500 text-xl font-bold">×</button>
        <h3 className="text-lg font-bold mb-4 text-red-700">Hapus Data Siswa</h3>
        <p className="mb-6">Apakah Anda yakin ingin menghapus data siswa ini? Tindakan ini tidak dapat dibatalkan.</p>
        <div className="flex justify-end gap-2">
          <button onClick={onClose} className="px-4 py-2 rounded bg-gray-200 text-gray-700 text-xs font-semibold hover:bg-gray-300">Batal</button>
          <button onClick={onDelete} className="px-6 py-2 bg-red-600 text-white rounded-md font-semibold text-xs shadow hover:bg-red-700">Hapus</button>
        </div>
      </div>
    </div>
  );
}

const PAGE_SIZE = 5;

export default function Walikelas() {
  const { user } = useAuth();
  const [students, setStudents] = useState<Student[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);
  const [selected, setSelected] = useState<Student | null>(null);
  const [search, setSearch] = useState('');
  const [schoolFilter, setSchoolFilter] = useState('');
  const [verifiedFilter, setVerifiedFilter] = useState<'all' | 'verified' | 'unverified'>('all');
  const [page, setPage] = useState(1);
  const [editModal, setEditModal] = useState<{open: boolean, student: Student | null}>({open: false, student: null});
  const [deleteModal, setDeleteModal] = useState<{open: boolean, student: Student | null}>({open: false, student: null});
  const [attendanceSessions, setAttendanceSessions] = useState<any[]>([]);
  const [attendanceRecords, setAttendanceRecords] = useState<any[]>([]);
  const [attendanceLoading, setAttendanceLoading] = useState(true);
  const [dateFilter, setDateFilter] = useState<{start: string, end: string}>({start: '', end: ''});
  const [editAttendanceModal, setEditAttendanceModal] = useState<{
    open: boolean;
    student: Student | null;
    session: any | null;
    currentStatus: string;
  }>({open: false, student: null, session: null, currentStatus: ''});
  // State untuk modal import siswa
  const [importModalOpen, setImportModalOpen] = useState(false);
  const [importClasses, setImportClasses] = useState<any[]>([]);
  const [selectedImportClass, setSelectedImportClass] = useState('');
  const [importStudents, setImportStudents] = useState<any[]>([]);
  const [importLoading, setImportLoading] = useState(false);
  const [classMap, setClassMap] = useState<{ [id: string]: string }>({});
  // Pagination state untuk Daftar Siswa
  const [studentPage, setStudentPage] = useState(1);
  const [studentPageSize, setStudentPageSize] = useState(PAGE_SIZE);
  // Pagination state untuk Rekap Kehadiran
  const [absenPage, setAbsenPage] = useState(1);
  const [absenPageSize, setAbsenPageSize] = useState(PAGE_SIZE);
  // Pagination state untuk Rekap Kehadiran Siswa
  const [rekapPage, setRekapPage] = useState(1);
  const [rekapPageSize, setRekapPageSize] = useState(PAGE_SIZE);
  // Pagination Rekap Kehadiran Siswa
  const rekapTotalItems = students.length;
  const rekapTotalPages = Math.ceil(rekapTotalItems / rekapPageSize) || 1;
  const rekapPaged = students.slice((rekapPage - 1) * rekapPageSize, rekapPage * rekapPageSize);

  // Pindahkan deklarasi fetchStudents ke sini
  const fetchStudents = async () => {
    setLoading(true);
    if (!user) {
      setStudents([]);
      setLoading(false);
      return;
    }
    const { data, error } = await supabase.from('students_extended').select('*').eq('created_by', user.id).order('full_name');
    if (error) setError(error.message);
    else setStudents(data || []);
    setLoading(false);
  };

  useEffect(() => {
    fetchStudents();
  }, [user]);

  useEffect(() => {
    // Fetch attendance_public_sessions & attendance_public_records
    const fetchAttendance = async () => {
      setAttendanceLoading(true);
      // Fetch all sessions (no class filter)
      const { data: sessions } = await supabase
        .from('attendance_public_sessions')
        .select('*')
        .order('session_date', { ascending: false });
      setAttendanceSessions(sessions || []);
      // Fetch all records for these sessions
      if (sessions && sessions.length > 0) {
        const sessionIds = sessions.map(s => s.id);
        const { data: records } = await supabase
          .from('attendance_public_records')
          .select('*')
          .in('attendance_session_id', sessionIds);
        setAttendanceRecords(records || []);
      } else {
        setAttendanceRecords([]);
      }
      setAttendanceLoading(false);
    };
    fetchAttendance();
  }, []);

  // Filtered & searched data
  const filtered = useMemo(() => {
    let data = students;
    if (search) {
      data = data.filter(s => s.full_name?.toLowerCase().includes(search.toLowerCase()));
    }
    if (schoolFilter) {
      data = data.filter(s => s.previous_school === schoolFilter);
    }
    if (verifiedFilter === 'verified') {
      data = data.filter(s => s.verified === true);
    } else if (verifiedFilter === 'unverified') {
      data = data.filter(s => s.verified !== true);
    }
    return data;
  }, [students, search, schoolFilter, verifiedFilter]);

  // Pagination Daftar Siswa
  const studentTotalItems = filtered.length;
  const studentTotalPages = Math.ceil(studentTotalItems / studentPageSize) || 1;
  const paged = filtered.slice((studentPage - 1) * studentPageSize, studentPage * studentPageSize);

  // Pagination Rekap Kehadiran
  const absenTotalItems = students.length;
  const absenTotalPages = Math.ceil(absenTotalItems / absenPageSize) || 1;
  const absenPaged = students.slice((absenPage - 1) * absenPageSize, absenPage * absenPageSize);

  // Info card
  const total = students.length;
  const totalL = students.filter(s => s.gender === 'Laki-laki').length;
  const totalP = students.filter(s => s.gender === 'Perempuan').length;

  // Unique schools for filter
  const schools = useMemo(() => {
    const set = new Set<string>();
    students.forEach(s => { if (s.previous_school) set.add(s.previous_school); });
    return Array.from(set).sort();
  }, [students]);

  // Reset page if filter/search berubah
  useEffect(() => { setPage(1); }, [search, schoolFilter, verifiedFilter]);

  // Reset absenPage jika filter/search/students berubah
  useEffect(() => {
    setAbsenPage(1);
  }, [search, schoolFilter, verifiedFilter, students]);

  const handleEdit = (student: Student) => setEditModal({open: true, student});
  const handleDelete = (student: Student) => setDeleteModal({open: true, student});
  
  // Handle edit attendance
  const handleEditAttendance = (student: Student, session: any, currentStatus: string) => {
    setEditAttendanceModal({
      open: true,
      student,
      session,
      currentStatus
    });
  };

  const saveAttendanceEdit = async (newStatus: string) => {
    const { student, session } = editAttendanceModal;
    if (!student || !session) return;

    // Check if record exists
    const existingRecord = attendanceRecords.find(r => 
      r.attendance_session_id === session.id && r.student_id === student.id
    );

    if (existingRecord) {
      // Update existing record
      const { error } = await supabase
        .from('attendance_public_records')
        .update({ status: newStatus })
        .eq('id', existingRecord.id);
      
      if (!error) {
        setAttendanceRecords(attendanceRecords.map(r => 
          r.id === existingRecord.id ? { ...r, status: newStatus } : r
        ));
        toast.success('Status kehadiran berhasil diperbarui');
      } else {
        toast.error('Gagal memperbarui status kehadiran');
      }
    } else {
      // Create new record
      const { error } = await supabase
        .from('attendance_public_records')
        .insert({
          attendance_session_id: session.id,
          student_id: student.id,
          status: newStatus
        });
      
      if (!error) {
        // Refresh attendance records
        const { data: newRecords } = await supabase
          .from('attendance_public_records')
          .select('*')
          .in('attendance_session_id', attendanceSessions.map(s => s.id));
        setAttendanceRecords(newRecords || []);
        toast.success('Status kehadiran berhasil ditambahkan');
      } else {
        toast.error('Gagal menambahkan status kehadiran');
      }
    }

    setEditAttendanceModal({open: false, student: null, session: null, currentStatus: ''});
  };

  const saveEdit = async (data: Student) => {
    const { error } = await supabase.from('students_extended').update(data).eq('id', data.id);
    if (!error) {
      setStudents(students.map(s => s.id === data.id ? {...s, ...data} : s));
      setEditModal({open: false, student: null});
      toast.success('Data siswa berhasil diperbarui');
    }
  };
  const confirmDelete = async () => {
    const student = deleteModal.student;
    if (!student) return;
    const { error } = await supabase.from('students_extended').delete().eq('id', student.id);
    if (!error) {
      setStudents(students.filter(s => s.id !== student.id));
      setDeleteModal({open: false, student: null});
      toast.success('Data siswa berhasil dihapus');
    }
  };

  // Tabs: Siswa | Rekap Kehadiran | Rekap Kehadiran Siswa
  const [tab, setTab] = useState<'siswa' | 'absen' | 'rekap-siswa'>('siswa');

  // Filter attendance sessions by date range
  const filteredAttendanceSessions = useMemo(() => {
    if (!dateFilter.start && !dateFilter.end) {
      return attendanceSessions;
    }
    
    return attendanceSessions.filter(session => {
      const sessionDate = new Date(session.session_date);
      const startDate = dateFilter.start ? new Date(dateFilter.start) : null;
      const endDate = dateFilter.end ? new Date(dateFilter.end) : null;
      
      if (startDate && endDate) {
        return sessionDate >= startDate && sessionDate <= endDate;
      } else if (startDate) {
        return sessionDate >= startDate;
      } else if (endDate) {
        return sessionDate <= endDate;
      }
      
      return true;
    });
  }, [attendanceSessions, dateFilter]);

  // Tambahkan fungsi import siswa
  const handleImportStudents = async () => {
    if (!user) {
      toast.error('User belum login');
      return;
    }
    // 1. Ambil data siswa dari tabel students (field: name, nis, class_id)
    const { data: students, error } = await supabase
      .from('students')
      .select('name, nis, class_id');
    if (error) {
      toast.error('Gagal mengambil data siswa');
      return;
    }
    // 2. Ambil NIS yang sudah ada di students_extended milik user login
    const { data: existing, error: errorExisting } = await supabase
      .from('students_extended')
      .select('nis')
      .eq('created_by', user.id);
    if (errorExisting) {
      toast.error('Gagal memeriksa duplikat NIS');
      return;
    }
    const existingNis = (existing || []).map(e => e.nis);
    // 3. Filter siswa yang belum ada di students_extended (berdasarkan NIS)
    const newStudents = (students || []).filter(s => !existingNis.includes(s.nis));
    if (newStudents.length === 0) {
      toast('Semua siswa sudah diambil');
      return;
    }
    // 4. Siapkan data untuk insert ke students_extended
    const dataToInsert = newStudents.map(s => ({
      full_name: s.name, // mapping name ke full_name
      nis: s.nis,
      class_id: s.class_id,
      created_by: user.id,
    }));
    // 5. Insert ke students_extended
    const { error: insertError } = await supabase
      .from('students_extended')
      .insert(dataToInsert);
    if (insertError) {
      toast.error('Gagal menyimpan data ke walikelas');
    } else {
      toast.success('Data siswa berhasil diambil ke walikelas');
      fetchStudents(); // refresh data siswa
    }
  };

  // Ambil daftar kelas dari tabel students milik user login
  const fetchImportClasses = async () => {
    if (!user) return;
    setImportLoading(true);
    // Ambil semua class_id unik dari tabel students milik user login
    const { data: studentData } = await supabase
      .from('students')
      .select('class_id')
      .eq('created_by', user.id);
    const uniqueClassIds = Array.from(new Set((studentData || []).map((s: any) => s.class_id))).filter(Boolean);
    // Ambil nama kelas dari tabel classes
    const { data: classData } = await supabase
      .from('classes')
      .select('id, name');
    // Buat mapping class_id ke nama kelas
    const map: { [id: string]: string } = {};
    (classData || []).forEach((c: any) => {
      map[c.id] = c.name;
    });
    setClassMap(map);
    setImportClasses(uniqueClassIds);
    setImportLoading(false);
  };

  // Ambil siswa dari kelas terpilih
  const fetchImportStudents = async (classId: string) => {
    if (!user || !classId) return;
    setImportLoading(true);
    const { data, error } = await supabase
      .from('students')
      .select('name, nis, class_id')
      .eq('created_by', user.id)
      .eq('class_id', classId);
    if (!error && data) setImportStudents(data);
    else setImportStudents([]);
    setImportLoading(false);
  };

  // Handler buka modal import
  const handleOpenImportModal = () => {
    setImportModalOpen(true);
    setSelectedImportClass('');
    setImportStudents([]);
    fetchImportClasses();
  };

  // Handler pilih kelas di modal
  const handleSelectImportClass = (classId: string) => {
    setSelectedImportClass(classId);
    setImportStudents([]);
    if (classId) fetchImportStudents(classId);
  };

  // Handler simpan ke students_extended
  const handleSaveImportStudents = async () => {
    if (!user || !selectedImportClass || importStudents.length === 0) return;
    setImportLoading(true);
    // Ambil NIS yang sudah ada di students_extended milik user login
    const { data: existing, error: errorExisting } = await supabase
      .from('students_extended')
      .select('nis')
      .eq('created_by', user.id);
    const existingNis = (existing || []).map((e: any) => e.nis);
    // Filter siswa yang belum ada di students_extended (berdasarkan NIS)
    const newStudents = (importStudents || []).filter(s => !existingNis.includes(s.nis));
    if (newStudents.length === 0) {
      toast('Semua siswa dari kelas ini sudah ada di walikelas');
      setImportLoading(false);
      return;
    }
    // Siapkan data untuk insert ke students_extended
    const dataToInsert = newStudents.map(s => ({
      full_name: s.name,
      nis: s.nis,
      class_id: s.class_id,
      created_by: user.id,
    }));
    // Insert ke students_extended
    const { error: insertError } = await supabase
      .from('students_extended')
      .insert(dataToInsert);
    setImportLoading(false);
    if (insertError) {
      toast.error('Gagal menyimpan data ke walikelas');
    } else {
      toast.success('Data siswa berhasil disalin ke walikelas');
      setImportModalOpen(false);
      fetchStudents();
    }
  };

  // Fungsi untuk mengkapitalkan setiap kata (Title Case)
  const capitalizeWords = (text: string | undefined): string => {
    if (!text) return '';
    return text
      .toLowerCase()
      .split(' ')
      .map(word => word.charAt(0).toUpperCase() + word.slice(1))
      .join(' ');
  };

  // Fungsi untuk mengubah teks menjadi huruf kapital semua (UPPERCASE)
  const toUpperCase = (text: string | undefined): string => {
    if (!text) return '';
    return text.toUpperCase();
  };

  // Fungsi Generate PDF Identitas Peserta Didik
  const generatePDFIdentitas = async (student: Student) => {
    const doc = new jsPDF();
    const pageWidth = doc.internal.pageSize.getWidth();
    const pageHeight = doc.internal.pageSize.getHeight();
    const marginLeft = 20; // 20mm margin kiri
    const marginRight = 20; // 20mm margin kanan
    const marginTop = 20; // 20mm margin atas
    const marginBottom = 10; // 10mm margin bawah
    const margin = marginLeft; // untuk kompatibilitas dengan kode existing
    let yPos = marginTop + 15; // Tambah margin top 15mm untuk "IDENTITAS PESERTA DIDIK"

    // Load image pp.jpg
    let photoBase64 = '';
    try {
      const response = await fetch('/pp.jpg');
      const blob = await response.blob();
      photoBase64 = await new Promise<string>((resolve) => {
        const reader = new FileReader();
        reader.onloadend = () => resolve(reader.result as string);
        reader.readAsDataURL(blob);
      });
    } catch (error) {
      console.error('Error loading photo:', error);
    }

    // Title
    doc.setFontSize(14);
    doc.setFont('helvetica', 'bold');
    doc.text('IDENTITAS PESERTA DIDIK', pageWidth / 2, yPos, { align: 'center' });
    
    yPos += 12;
    doc.setFontSize(11);
    
    // Gunakan DejaVu Sans Condensed untuk data siswa
    await setDejaVuFont(doc, 'normal');

    // Helper function untuk menambah baris
    const addRow = (no: string, label: string, value: string, isSubItem: boolean = false) => {
      const xNo = margin;
      const xLabel = margin + 10; // Jarak nomor ke teks lebih dekat
      const xColon = margin + 70; // Titik dua lebih dekat
      const xValue = margin + 75; // Value mulai setelah titik dua
      
      // Nomor
      if (no) {
        doc.text(no, xNo, yPos);
      }
      
      // Label - semua sejajar di posisi yang sama
      doc.text(label, xLabel, yPos);
      
      // Colon
      doc.text(':', xColon, yPos);
      
      // Value dengan word wrap
      if (value) {
        const maxWidth = pageWidth - xValue - margin;
        const lines = doc.splitTextToSize(value, maxWidth);
        // Render setiap baris dengan spacing 5px antar baris dalam value
        lines.forEach((line: string, i: number) => {
          doc.text(line, xValue, yPos + (i * 5));
        });
        // Tambah spacing ke baris berikutnya: 7px dari baris terakhir
        yPos += (lines.length - 1) * 5 + 7;
      } else {
        yPos += 7;
      }
    };

    // Format tanggal lahir
    const formatDate = (dateString: string | undefined) => {
      if (!dateString) return '';
      const date = new Date(dateString);
      const day = date.getDate();
      const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
      const month = months[date.getMonth()];
      const year = date.getFullYear();
      return `${day} ${month} ${year}`;
    };

    // Data siswa
    addRow('1.', 'Nama Lengkap Peserta Didik', student.full_name || '');
    addRow('2.', 'Nomor Induk/NISN', `${student.nis || ''} / ${student.nisn || ''}`);
    
    // Handle birth date - check if it's already formatted or needs formatting
    let birthDateFormatted = '';
    if (student.birth_date) {
      // If birth_date contains numbers and dashes (like 2010-03-14), format it
      if (student.birth_date.match(/^\d{4}-\d{2}-\d{2}$/)) {
        birthDateFormatted = formatDate(student.birth_date);
      } else {
        // If it's already formatted (like "14 maret 2010"), use as is
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
    
    const today = new Date();
    const todayStr = formatDate(today.toISOString());
    addRow('', 'Pada tanggal', '14 Juli 2025');
    
    addRow('12.', 'Nama Orang Tua', '');
    addRow('', 'a. Ayah', capitalizeWords(student.father_name), true);
    addRow('', 'b. Ibu', capitalizeWords(student.mother_name), true);
    
    addRow('13.', 'Alamat Orang Tua', student.parent_address || '');
    addRow('', 'Nomor Telepon Rumah', student.parent_whatsapp || '', true);
    
    addRow('14.', 'Pekerjaan Orang Tua :', '');
    addRow('', 'a. Ayah', student.father_job || '', true);
    addRow('', 'b. Ibu', student.mother_job || '', true);
    
    addRow('15.', 'Nama Wali Siswa', '');
    addRow('16.', 'Alamat Wali Peserta Didik', '');
    addRow('', 'Nomor Telepon Rumah', '', true);
    addRow('17.', 'Pekerjaan Wali Peserta Didik', '');

    // Tanda tangan - tambah jarak lebih banyak ke bawah
    yPos += 13; // Tambah jarak dari 10 menjadi 20
    const signatureStartY = yPos;
    const photoX = margin + 47; // Foto lebih ke kiri (dari 50 menjadi 20)
    const signatureX = pageWidth - 100;
    
    // Tambahkan foto di sebelah kiri
    if (photoBase64) {
      const photoWidth = 30;
      const photoHeight = 40;
      doc.addImage(photoBase64, 'JPEG', photoX, signatureStartY, photoWidth, photoHeight);
    }
    
    // Reset yPos agar tanda tangan mulai dari posisi yang sama dengan foto
    // Tambahkan offset untuk baseline teks agar sejajar dengan top foto
    yPos = signatureStartY + 4;
    
    // Tanggal dan jabatan - gunakan DejaVu Sans Condensed
    await setDejaVuFont(doc, 'normal');
    doc.setFontSize(11);
    // doc.text(`Majalengka, ${todayStr}`, signatureX, yPos);
    doc.text(`Bantarujeg, 14 Juli 2025`, signatureX, yPos);
    yPos += 5;
    doc.text('Kepala Sekolah', signatureX, yPos);
    
    // Nama dengan DejaVu Sans Condensed bold dan underline
    yPos += 24;
    await setDejaVuFont(doc, 'bold');
    doc.setFontSize(10);
    const namaKepala = 'Dr. H. Toto Warsito, S.Ag., M.Ag.';
    doc.text(namaKepala, signatureX, yPos);
    
    // Tambah underline manual dengan error handling
    try {
      const textWidth = doc.getTextWidth(namaKepala);
      doc.line(signatureX, yPos + 1, signatureX + textWidth, yPos + 1);
    } catch (error) {
      console.warn('⚠️ Error getting text width, using fallback underline:', error);
      // Fallback: gunakan panjang estimasi berdasarkan jumlah karakter
      const estimatedWidth = namaKepala.length * 2.5; // Estimasi 2.5pt per karakter
      doc.line(signatureX, yPos + 1, signatureX + estimatedWidth, yPos + 1);
    }
    
    // NIP dengan DejaVu Sans Condensed bold
    yPos += 5;
    await setDejaVuFont(doc, 'bold');
    doc.setFontSize(10);
    doc.text('NIP. 19730302 199802 1 002', signatureX, yPos);

    // Save PDF
    const fileName = `Identitas_${student.full_name?.replace(/\s+/g, '_') || 'Siswa'}.pdf`;
    doc.save(fileName);
    toast.success('PDF berhasil diunduh');
  };

  // Fungsi untuk format nama (jika 3 kata, kata terakhir hanya huruf depan)
  const formatNameForLabel = (fullName: string): string => {
    if (!fullName) return '';
    
    const words = fullName.trim().split(' ').filter(word => word.length > 0);
    
    if (words.length >= 3) {
      // Jika 3 kata atau lebih, kata terakhir hanya huruf depan
      const firstWords = words.slice(0, -1).join(' ');
      const lastWordInitial = words[words.length - 1].charAt(0);
      return `${firstWords} ${lastWordInitial}`.toUpperCase();
    } else {
      // Jika kurang dari 3 kata, tampilkan semua
      return words.join(' ').toUpperCase();
    }
  };

  // Fungsi Generate Label Nama
  const generateNameLabels = async () => {
    if (filtered.length === 0) {
      toast.error('Tidak ada data siswa untuk dicetak');
      return;
    }

    // Check if any students have names
    const studentsWithNames = filtered.filter(s => s.full_name && s.full_name.trim().length > 0);
    if (studentsWithNames.length === 0) {
      toast.error('Tidak ada siswa dengan nama lengkap untuk dicetak');
      return;
    }

    toast.loading('Membuat label nama...');
    
    const doc = new jsPDF('landscape', 'mm', [215, 330]); // F4 Landscape untuk 4 kolom
    const pageWidth = doc.internal.pageSize.getWidth(); // ~330mm
    const pageHeight = doc.internal.pageSize.getHeight(); // ~215mm
    
    // Ukuran label: 7cm x 3.5cm
    const labelWidth = 70; // 7cm
    const labelHeight = 35; // 3.5cm
    const labelsPerRow = 4;
    const labelsPerColumn = 5; // Fixed 5 rows per page (F4 paper) - safe margin
    const labelsPerPage = labelsPerRow * labelsPerColumn;
    
    // Calculate margins to ensure all labels fit
    // F4 landscape: 330mm width x 215mm height
    const totalLabelsWidth = labelsPerRow * labelWidth; // 4 x 70 = 280mm
    const totalLabelsHeight = labelsPerColumn * labelHeight; // 5 x 35 = 175mm
    
    // Margin dan spacing
    const marginLeft = (pageWidth - totalLabelsWidth) / 2; // Center horizontally
    const marginTop = Math.max(5, (pageHeight - totalLabelsHeight) / 2); // Center vertically with minimum 5mm
    
    // Setup font
    await setDejaVuFont(doc, 'bold');
    
    let currentPage = 0;
    
    // Use only students with names
    const studentsToProcess = filtered.filter(s => s.full_name && s.full_name.trim().length > 0);
    
    for (let i = 0; i < studentsToProcess.length; i++) {
      const student = studentsToProcess[i];
      const labelIndex = i % labelsPerPage;
      
      // Add new page if needed
      if (i > 0 && labelIndex === 0) {
        doc.addPage();
        currentPage++;
      }
      
      // Calculate position
      const row = Math.floor(labelIndex / labelsPerRow);
      const col = labelIndex % labelsPerRow;
      
      const x = marginLeft + (col * labelWidth);
      const y = marginTop + (row * labelHeight);
      
      // Draw label border
      doc.setDrawColor(0, 0, 0);
      doc.setLineWidth(0.5);
      doc.rect(x, y, labelWidth, labelHeight);
      
      // Format name
      const formattedName = formatNameForLabel(student.full_name || '');
      const nisText = `NIS : ${(student.nis || 'TIDAK ADA').toUpperCase()}`;
      
      // Text positioning
      const textX = x + (labelWidth / 2); // Center horizontally
      const nameY = y + (labelHeight / 2) - 3; // Center vertically, slightly up
      const nisY = y + (labelHeight / 2) + 5; // Below name
      
      // Draw name (larger font, bold)
      await setDejaVuFont(doc, 'bold');
      doc.setFontSize(16); // Increased from 12 to 16
      
      // Handle long names by wrapping text if needed
      const maxWidth = labelWidth - 4; // Leave 2mm margin on each side
      const nameLines = doc.splitTextToSize(formattedName, maxWidth);
      
      if (nameLines.length > 1) {
        // If name is too long, use smaller font
        doc.setFontSize(14); // Increased from 10 to 14
        const adjustedNameLines = doc.splitTextToSize(formattedName, maxWidth);
        adjustedNameLines.forEach((line: string, index: number) => {
          doc.text(line, textX, nameY + (index * 5), { align: 'center' }); // Increased line spacing from 4 to 5
        });
      } else {
        doc.text(formattedName, textX, nameY, { align: 'center' });
      }
      
      // Draw NIS (larger font, normal weight)
      await setDejaVuFont(doc, 'normal');
      doc.setFontSize(12); // Increased from 9 to 12
      doc.text(nisText, textX, nisY, { align: 'center' });
    }

    // Save PDF
    doc.save('Label_Nama_Siswa.pdf');
    toast.dismiss();
    toast.success(`Label nama untuk ${studentsToProcess.length} siswa berhasil dibuat`);
  };

  // Fungsi Generate PDF untuk semua siswa dalam 1 file
  const generatePDFAllStudents = async () => {
    if (filtered.length === 0) {
      toast.error('Tidak ada data siswa untuk dicetak');
      return;
    }

    toast.loading('Membuat PDF...');
    
    const doc = new jsPDF();
    const pageWidth = doc.internal.pageSize.getWidth();
    const pageHeight = doc.internal.pageSize.getHeight();
    const marginLeft = 20; // 20mm margin kiri
    const marginRight = 20; // 20mm margin kanan
    const marginTop = 20; // 20mm margin atas
    const marginBottom = 10; // 10mm margin bawah
    const margin = marginLeft; // untuk kompatibilitas dengan kode existing

    // Load image pp.jpg sekali saja
    let photoBase64 = '';
    try {
      const response = await fetch('/pp.jpg');
      const blob = await response.blob();
      photoBase64 = await new Promise<string>((resolve) => {
        const reader = new FileReader();
        reader.onloadend = () => resolve(reader.result as string);
        reader.readAsDataURL(blob);
      });
    } catch (error) {
      console.error('Error loading photo:', error);
    }

    // Helper function untuk format tanggal
    const formatDate = (dateString: string | undefined) => {
      if (!dateString) return '';
      const date = new Date(dateString);
      const day = date.getDate();
      const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
      const month = months[date.getMonth()];
      const year = date.getFullYear();
      return `${day} ${month} ${year}`;
    };

    // Setup DejaVu font sekali di awal
    await setDejaVuFont(doc, 'normal');

    // Loop untuk setiap siswa
    for (let index = 0; index < filtered.length; index++) {
      const student = filtered[index];
      
      // Tambah halaman baru untuk siswa kedua dan seterusnya
      if (index > 0) {
        doc.addPage();
      }

      let yPos = marginTop + 5; // Tambah margin top 5mm untuk "IDENTITAS PESERTA DIDIK"

      // Title
      doc.setFontSize(14);
      doc.setFont('helvetica', 'bold');
      doc.text('IDENTITAS PESERTA DIDIK', pageWidth / 2, yPos, { align: 'center' });
      
      yPos += 12;
      doc.setFontSize(11);
      
      // Gunakan DejaVu Sans Condensed untuk data siswa
      await setDejaVuFont(doc, 'normal');

      // Helper function untuk menambah baris
      const addRow = (no: string, label: string, value: string) => {
        const xNo = margin;
        const xLabel = margin + 10;
        const xColon = margin + 70;
        const xValue = margin + 75;
        
        if (no) {
          doc.text(no, xNo, yPos);
        }
        
        doc.text(label, xLabel, yPos);
        doc.text(':', xColon, yPos);
        
        if (value) {
          const maxWidth = pageWidth - xValue - margin;
          const lines = doc.splitTextToSize(value, maxWidth);
          // Render setiap baris dengan spacing 5px antar baris dalam value
          lines.forEach((line: string, i: number) => {
            doc.text(line, xValue, yPos + (i * 5));
          });
          // Tambah spacing ke baris berikutnya: 7px dari baris terakhir
          yPos += (lines.length - 1) * 5 + 7;
        } else {
          yPos += 7;
        }
      };

      // Data siswa
      addRow('1.', 'Nama Lengkap Peserta Didik', student.full_name || '');
      addRow('2.', 'Nomor Induk/NISN', `${student.nis || ''} / ${student.nisn || ''}`);
      
      // Handle birth date - check if it's already formatted or needs formatting
      let birthDateFormatted = '';
      if (student.birth_date) {
        // If birth_date contains numbers and dashes (like 2010-03-14), format it
        if (student.birth_date.match(/^\d{4}-\d{2}-\d{2}$/)) {
          birthDateFormatted = formatDate(student.birth_date);
        } else {
          // If it's already formatted (like "14 maret 2010"), use as is
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
      addRow('', 'Nomor Telepon Rumah', student.parent_whatsapp || '');
      
      addRow('14.', 'Pekerjaan Orang Tua :', '');
      addRow('', 'a. Ayah', student.father_job || '');
      addRow('', 'b. Ibu', student.mother_job || '');
      
      addRow('15.', 'Nama Wali Siswa', '');
      addRow('16.', 'Alamat Wali Peserta Didik', '');
      addRow('', 'Nomor Telepon Rumah', '');
      addRow('17.', 'Pekerjaan Wali Peserta Didik', '');

      // Tanda tangan - tambah jarak lebih banyak ke bawah
      yPos += 13; // Tambah jarak untuk TTD
      const signatureStartY = yPos;
      const photoX = margin + 47; // Foto lebih ke kiri 
      const signatureX = pageWidth - 100;
      
      if (photoBase64) {
        const photoWidth = 30;
        const photoHeight = 40;
        doc.addImage(photoBase64, 'JPEG', photoX, signatureStartY, photoWidth, photoHeight);
      }
      
      yPos = signatureStartY + 4;
      
      // Tanggal dan jabatan - gunakan DejaVu Sans Condensed
      await setDejaVuFont(doc, 'normal');
      doc.setFontSize(11);
      doc.text(`Majalengka, 14 Juli 2025`, signatureX, yPos);
      yPos += 5;
      doc.text('Kepala Sekolah', signatureX, yPos);
      
      yPos += 24;
      await setDejaVuFont(doc, 'bold');
      doc.setFontSize(10);
      const namaKepala = 'Dr. H. Toto Warsito, S.Ag., M.Ag.';
      doc.text(namaKepala, signatureX, yPos);
      
      // Tambah underline manual dengan error handling
      try {
        const textWidth = doc.getTextWidth(namaKepala);
        doc.line(signatureX, yPos + 1, signatureX + textWidth, yPos + 1);
      } catch (error) {
        console.warn('⚠️ Error getting text width, using fallback underline:', error);
        // Fallback: gunakan panjang estimasi berdasarkan jumlah karakter
        const estimatedWidth = namaKepala.length * 2.5; // Estimasi 2.5pt per karakter
        doc.line(signatureX, yPos + 1, signatureX + estimatedWidth, yPos + 1);
      }
      
      yPos += 5;
      await setDejaVuFont(doc, 'bold');
      doc.text('NIP. 19730302 199802 1 002', signatureX, yPos);
    }

    // Save PDF dengan semua siswa
    doc.save('Identitas_Semua_Siswa.pdf');
    toast.dismiss();
    toast.success(`PDF dengan ${filtered.length} siswa berhasil dibuat`);
  };

  return (
    <div className="min-h-screen bg-gradient-to-br from-green-50 via-white to-blue-50">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {/* Header Section */}
        <div className="mb-8">
          <div className="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            <div className="flex items-center gap-3">
              <GraduationCap className="h-10 w-10 text-blue-700 -mt-1" />
              <div>
                <h1 className="text-3xl font-bold text-gray-900 mb-1">Daftar Siswa (Walikelas)</h1>
                <p className="text-gray-600">Klik tombol detail untuk melihat biodata lengkap siswa.</p>
              </div>
            </div>
            <div className="flex items-center justify-end gap-2">
              <Link to="/formulir-siswa" className="inline-block px-4 py-2 bg-blue-600 text-white text-xs font-semibold rounded-lg shadow hover:bg-blue-700 transition-colors flex items-center gap-2">
                <UserPlus className="h-4 w-4" />
                Formulir Siswa Publik
              </Link>
              <Link to="/absen" className="inline-block px-4 py-2 bg-green-600 text-white text-xs font-semibold rounded-lg shadow hover:bg-green-700 transition-colors flex items-center gap-2 ml-3">
                <Calendar className="h-4 w-4" />
                Absen
              </Link>
            </div>
          </div>
        </div>
        {/* Modal Import Siswa */}
        {importModalOpen && (
          <Modal isOpen={importModalOpen} onClose={() => setImportModalOpen(false)} title="Import Siswa dari Database Utama" size="lg">
            <div className="mb-4">
              <label className="block text-sm font-medium text-gray-700 mb-2">Pilih Kelas</label>
              <select
                value={selectedImportClass}
                onChange={e => handleSelectImportClass(e.target.value)}
                className="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-400 focus:border-blue-500"
                disabled={importLoading}
              >
                <option value="">-- Pilih Kelas --</option>
                {importClasses.map((cid: any) => (
                  <option key={cid} value={cid}>{classMap[cid] || cid}</option>
                ))}
              </select>
            </div>
            {importLoading ? (
              <div className="text-center py-6 text-gray-500">Memuat data...</div>
            ) : selectedImportClass && (
              <div>
                <div className="mb-2 font-semibold text-gray-700">Daftar Siswa di Kelas {classMap[selectedImportClass] || selectedImportClass}:</div>
                {importStudents.length === 0 ? (
                  <div className="text-gray-500 text-sm">Tidak ada siswa di kelas ini.</div>
                ) : (
                  <ul className="mb-4 max-h-48 overflow-y-auto divide-y divide-gray-100">
                    {importStudents.map((s, i) => (
                      <li key={s.nis || i} className="py-1 flex justify-between items-center">
                        <span>{s.name} <span className="text-xs text-gray-400">({s.nis})</span></span>
                        <span className="text-xs text-gray-500">{s.class_id}</span>
                      </li>
                    ))}
                  </ul>
                )}
                <button
                  onClick={handleSaveImportStudents}
                  className="px-5 py-2 bg-blue-600 text-white rounded-lg font-semibold text-xs shadow hover:bg-blue-700 disabled:opacity-50"
                  disabled={importStudents.length === 0 || importLoading}
                >
                  Simpan ke Walikelas
                </button>
              </div>
            )}
          </Modal>
        )}
        {/* Tabs */}
        <div className="mb-8 flex gap-1 md:gap-2">
          <button onClick={() => setTab('siswa')} className={`px-2 md:px-4 py-1 md:py-2 rounded-t-lg font-semibold text-xs md:text-sm border-b-2 ${tab === 'siswa' ? 'border-blue-600 text-blue-700 bg-white' : 'border-transparent text-gray-500 bg-gray-100'}`}>Daftar Siswa</button>
          <button onClick={() => setTab('absen')} className={`px-2 md:px-4 py-1 md:py-2 rounded-t-lg font-semibold text-xs md:text-sm border-b-2 ${tab === 'absen' ? 'border-blue-600 text-blue-700 bg-white' : 'border-transparent text-gray-500 bg-gray-100'}`}>Rekap Kehadiran</button>
          <button onClick={() => setTab('rekap-siswa')} className={`px-2 md:px-4 py-1 md:py-2 rounded-t-lg font-semibold text-xs md:text-sm border-b-2 ${tab === 'rekap-siswa' ? 'border-blue-600 text-blue-700 bg-white' : 'border-transparent text-gray-500 bg-gray-100'}`}>Rekap Kehadiran Siswa</button>
        </div>
        {tab === 'siswa' && (
          <>
            {/* Statistics Cards */}
            <div className="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
              <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
                <div className="flex items-center">
                  <div className="p-2 bg-blue-100 rounded-lg">
                    <Users className="h-6 w-6 text-blue-600" />
                  </div>
                  <div className="ml-4">
                    <p className="text-sm font-medium text-gray-600">Total Siswa</p>
                    <p className="text-2xl font-bold text-gray-900">{total}</p>
                  </div>
                </div>
              </div>
              <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
                <div className="flex items-center">
                  <div className="p-2 bg-green-100 rounded-lg">
                    <User className="h-6 w-6 text-green-600" />
                  </div>
                  <div className="ml-4">
                    <p className="text-sm font-medium text-gray-600">Laki-laki</p>
                    <p className="text-2xl font-bold text-gray-900">{totalL}</p>
                  </div>
                </div>
              </div>
              <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
                <div className="flex items-center">
                  <div className="p-2 bg-purple-100 rounded-lg">
                    <UserRound className="h-6 w-6 text-purple-600" />
                  </div>
                  <div className="ml-4">
                    <p className="text-sm font-medium text-gray-600">Perempuan</p>
                    <p className="text-2xl font-bold text-gray-900">{totalP}</p>
                  </div>
                </div>
              </div>
            </div>
            {/* Card Table */}
            <div className="bg-white shadow-sm rounded-xl border border-gray-200 overflow-hidden">
              <div className="px-4 sm:px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-gray-100">
                <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                  <div>
                    <h3 className="text-lg font-semibold text-gray-900">Daftar Siswa</h3>
                    <p className="text-sm text-gray-600">{filtered.length} dari {students.length} siswa</p>
                  </div>
                  <div className="flex items-center space-x-2">
                    <Filter className="h-4 w-4 text-gray-400" />
                    <span className="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                      {paged.length} ditampilkan
                    </span>
                    {/* Tombol Copy Siswa */}
                    <button onClick={handleOpenImportModal} className="inline-flex items-center gap-2 px-3 py-2 bg-green-600 text-white text-xs font-semibold rounded-lg shadow hover:bg-green-700 transition-colors">
                      <Plus className="h-4 w-4" />
                      Copy Siswa
                    </button>
                    {/* Tombol Generate PDF */}
                    <button onClick={generatePDFAllStudents} className="inline-flex items-center gap-2 px-3 py-2 bg-red-600 text-white text-xs font-semibold rounded-lg shadow hover:bg-red-700 transition-colors">
                      <Download className="h-4 w-4" />
                      PDF Semua
                    </button>
                    {/* Tombol Label Nama */}
                    <button onClick={generateNameLabels} className="inline-flex items-center gap-2 px-3 py-2 bg-purple-600 text-white text-xs font-semibold rounded-lg shadow hover:bg-purple-700 transition-colors">
                      <FileText className="h-4 w-4" />
                      Label Nama
                    </button>
                  </div>
                </div>
              </div>
              <div className="p-4 sm:p-6 border-b border-gray-200">
                <div className="flex flex-col md:flex-row md:items-center gap-2">
                  <div className="relative w-full md:w-64">
                    <div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                      <Search className="h-5 w-5 text-gray-400" />
                    </div>
                    <input
                      type="text"
                      placeholder="Cari nama siswa..."
                      value={search}
                      onChange={e => setSearch(e.target.value)}
                      className="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg leading-5 bg-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                    />
                  </div>
                  <select
                    value={schoolFilter}
                    onChange={e => setSchoolFilter(e.target.value)}
                    className="border border-gray-300 rounded-lg px-3 py-3 text-sm bg-white w-full md:w-64 focus:ring-2 focus:ring-blue-500"
                  >
                    <option value="">Filter asal sekolah</option>
                    {schools.map(s => (
                      <option key={s} value={s}>{s}</option>
                    ))}
                  </select>
                  <select
                    value={verifiedFilter}
                    onChange={e => setVerifiedFilter(e.target.value as 'all' | 'verified' | 'unverified')}
                    className="border border-gray-300 rounded-lg px-3 py-3 text-sm bg-white w-full md:w-64 focus:ring-2 focus:ring-blue-500"
                  >
                    <option value="all">Semua Status</option>
                    <option value="verified">Sudah Verified</option>
                    <option value="unverified">Belum Verified</option>
                  </select>
                </div>
              </div>
              <div className="overflow-x-auto hidden md:block">
                <table className="min-w-full divide-y divide-gray-200">
                  <thead className="bg-gradient-to-r from-blue-50 to-indigo-50">
                    <tr>
                      <th className="px-2 py-1.5 text-left text-[10px] font-semibold text-gray-600 uppercase tracking-wider">
                        <div className="flex items-center gap-1">
                          <div className="w-1.5 h-1.5 bg-blue-500 rounded-full"></div>
                          <span>No</span>
                        </div>
                      </th>
                      <th className="px-2 py-1.5 text-left text-[10px] font-semibold text-gray-600 uppercase tracking-wider">
                        <div className="flex items-center gap-1">
                          <User className="h-3 w-3 text-blue-500" />
                          <span>Nama Lengkap</span>
                        </div>
                      </th>
                      <th className="px-2 py-1.5 text-left text-[10px] font-semibold text-gray-600 uppercase tracking-wider">
                        <div className="flex items-center gap-1">
                          <UserRound className="h-3 w-3 text-purple-500" />
                          <span>Jenis Kelamin</span>
                        </div>
                      </th>
                      <th className="px-2 py-1.5 text-left text-[10px] font-semibold text-gray-600 uppercase tracking-wider">
                        <div className="flex items-center gap-1">
                          <div className="w-1.5 h-1.5 bg-yellow-500 rounded-full"></div>
                          <span>No HP</span>
                        </div>
                      </th>
                      <th className="px-2 py-1.5 text-left text-[10px] font-semibold text-gray-600 uppercase tracking-wider">
                        <div className="flex items-center gap-1">
                          <div className="w-1.5 h-1.5 bg-purple-500 rounded-full"></div>
                          <span>WA Ortu</span>
                        </div>
                      </th>
                      <th className="px-2 py-1.5 text-left text-[10px] font-semibold text-gray-600 uppercase tracking-wider">
                        <div className="flex items-center gap-1">
                          <Clock className="h-3 w-3 text-orange-500" />
                          <span>Update</span>
                        </div>
                      </th>
                      <th className="px-2 py-1.5 text-center text-[10px] font-semibold text-gray-600 uppercase tracking-wider">
                        <div className="flex items-center justify-center gap-1">
                          <CheckCircle className="h-3 w-3 text-green-500" />
                          <span>Verified</span>
                        </div>
                      </th>
                      <th className="px-2 py-1.5 text-center text-[10px] font-semibold text-gray-600 uppercase tracking-wider">
                        <div className="flex items-center justify-center gap-1">
                          <div className="w-1.5 h-1.5 bg-gray-500 rounded-full"></div>
                          <span>Aksi</span>
                        </div>
                      </th>
                    </tr>
                  </thead>
                  <tbody className="bg-white divide-y divide-gray-100">
                    {paged.map((s, i) => (
                      <tr key={s.id} className="hover:bg-gradient-to-r hover:from-blue-50 hover:to-indigo-50 transition-all duration-200">
                        <td className="px-2 py-1.5 whitespace-nowrap">
                          <div className="flex items-center justify-center w-5 h-5 bg-gradient-to-br from-blue-500 to-blue-600 rounded text-white text-[10px] font-bold shadow-sm">
                            {(studentPage - 1) * studentPageSize + i + 1}
                          </div>
                        </td>
                        <td className="px-2 py-1.5">
                          <div className="font-semibold text-gray-900 text-xs">{s.full_name}</div>
                          <div className="text-[10px] text-gray-500 flex items-center gap-0.5">
                            <User className="h-2.5 w-2.5" />
                            NIS: {s.nis || 'Tidak ada data'}
                          </div>
                        </td>
                        <td className="px-2 py-1.5">
                          <div className="inline-flex items-center px-1.5 py-0.5 rounded-full text-[10px] font-semibold bg-purple-50 text-purple-700 border border-purple-200">
                            {s.gender || 'Tidak ada data'}
                          </div>
                        </td>
                        <td className="px-2 py-1.5">
                          <div className="inline-flex items-center px-1.5 py-0.5 rounded-full text-[10px] font-semibold bg-yellow-50 text-yellow-700 border border-yellow-200">
                            {s.phone_number || 'Tidak ada data'}
                          </div>
                        </td>
                        <td className="px-2 py-1.5">
                          <div className="inline-flex items-center px-1.5 py-0.5 rounded-full text-[10px] font-semibold bg-purple-50 text-purple-700 border border-purple-200">
                            {s.parent_whatsapp || 'Tidak ada data'}
                          </div>
                        </td>
                        <td className="px-2 py-1.5">
                          <div className="flex items-center gap-1">
                            <Clock className="h-3 w-3 text-orange-500" />
                            <span className="text-[10px] font-medium text-gray-700">{getRelativeTime(s.updated_at)}</span>
                          </div>
                        </td>
                        <td className="px-2 py-1.5 text-center">
                          {s.verified ? (
                            <div className="inline-flex items-center px-1.5 py-0.5 rounded-full text-[10px] font-semibold bg-green-50 text-green-700 border border-green-200">
                              <CheckCircle className="h-2.5 w-2.5 mr-0.5" />
                              Verified
                            </div>
                          ) : (
                            <div className="inline-flex items-center px-1.5 py-0.5 rounded-full text-[10px] font-semibold bg-gray-50 text-gray-500 border border-gray-200">
                              <XCircle className="h-2.5 w-2.5 mr-0.5" />
                              Belum
                            </div>
                          )}
                        </td>
                        <td className="px-2 py-1.5 text-center">
                          <div className="flex justify-center items-center gap-0.5">
                            <button onClick={() => setSelected(s)} className="p-1 bg-blue-50 text-blue-600 rounded shadow-sm hover:bg-blue-100 transition-colors" title="Detail">
                              <Eye className="h-3 w-3" />
                            </button>
                            <button onClick={() => handleEdit(s)} className="p-1 bg-yellow-50 text-yellow-600 rounded shadow-sm hover:bg-yellow-100 transition-colors" title="Edit">
                              <Edit className="h-3 w-3" />
                            </button>
                            <button onClick={() => generatePDFIdentitas(s)} className="p-1 bg-purple-50 text-purple-600 rounded shadow-sm hover:bg-purple-100 transition-colors" title="PDF">
                              <Download className="h-3 w-3" />
                            </button>
                            <button onClick={() => handleDelete(s)} className="p-1 bg-red-50 text-red-600 rounded shadow-sm hover:bg-red-100 transition-colors" title="Hapus">
                              <Trash2 className="h-3 w-3" />
                            </button>
                          </div>
                        </td>
                      </tr>
                    ))}
                    {paged.length === 0 && (
                      <tr>
                        <td colSpan={8} className="text-center py-8">
                          <div className="flex flex-col items-center gap-3">
                            <div className="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center">
                              <Users className="h-8 w-8 text-gray-400" />
                            </div>
                            <div>
                              <h4 className="text-lg font-medium text-gray-700 mb-1">Tidak Ada Data Siswa</h4>
                              <p className="text-gray-500 text-sm">Coba ubah filter atau tambah data siswa baru.</p>
                            </div>
                          </div>
                        </td>
                      </tr>
                    )}
                  </tbody>
                </table>
              </div>
              {/* Card List Mobile */}
              {paged.length > 0 && (
                <div className="md:hidden p-2">
                  <div className="space-y-2">
                    {paged.map((s, i) => (
                      <div key={s.id} className="bg-white rounded-xl shadow-sm border border-gray-100 p-3">
                        <div className="flex items-center gap-3 mb-3">
                          <div className="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center text-white text-xs font-bold shadow-sm">
                            {(studentPage - 1) * studentPageSize + i + 1}
                          </div>
                          <div className="flex-1 min-w-0">
                            <div className="font-semibold text-gray-900 text-sm truncate">{s.full_name}</div>
                            <div className="text-xs text-gray-500 flex items-center gap-1">
                              <User className="h-3 w-3" />
                              NIS: {s.nis || 'Tidak ada data'}
                            </div>
                          </div>
                        </div>
                        
                        <div className="bg-gradient-to-r from-gray-50 to-gray-100 rounded-lg p-3">
                          <div className="text-xs font-medium text-gray-600 mb-2 flex items-center gap-1">
                            <GraduationCap className="h-3 w-3" />
                            Informasi Siswa
                          </div>
                          
                          <div className="grid grid-cols-1 gap-2">
                            <div className="bg-white rounded-lg p-2 border border-gray-200">
                              <div className="flex items-center justify-between">
                                <div className="flex items-center gap-2">
                                  <div className="w-2 h-2 bg-blue-500 rounded-full"></div>
                                  <span className="text-xs font-medium text-blue-700">Jenis Kelamin</span>
                                </div>
                                <span className="text-sm font-bold text-gray-700">{s.gender || 'Tidak ada data'}</span>
                              </div>
                            </div>
                            
                            <div className="bg-white rounded-lg p-2 border border-gray-200">
                              <div className="flex items-center justify-between">
                                <div className="flex items-center gap-2">
                                  <div className="w-2 h-2 bg-green-500 rounded-full"></div>
                                  <span className="text-xs font-medium text-green-700">No HP</span>
                                </div>
                                <span className="text-sm font-bold text-gray-700">{s.phone_number || 'Tidak ada data'}</span>
                              </div>
                            </div>
                            
                            <div className="bg-white rounded-lg p-2 border border-gray-200">
                              <div className="flex items-center justify-between">
                                <div className="flex items-center gap-2">
                                  <div className="w-2 h-2 bg-purple-500 rounded-full"></div>
                                  <span className="text-xs font-medium text-purple-700">WA Ortu</span>
                                </div>
                                <span className="text-sm font-bold text-gray-700">{s.parent_whatsapp || 'Tidak ada data'}</span>
                              </div>
                            </div>
                            
                            <div className="bg-white rounded-lg p-2 border border-gray-200">
                              <div className="flex items-center justify-between">
                                <div className="flex items-center gap-2">
                                  <Clock className="h-3 w-3 text-orange-500" />
                                  <span className="text-xs font-medium text-orange-700">Update</span>
                                </div>
                                <span className="text-sm font-bold text-gray-700">{getRelativeTime(s.updated_at)}</span>
                              </div>
                            </div>
                            
                            <div className="bg-white rounded-lg p-2 border border-gray-200">
                              <div className="flex items-center justify-between">
                                <div className="flex items-center gap-2">
                                  <CheckCircle className="h-3 w-3 text-green-500" />
                                  <span className="text-xs font-medium text-green-700">Verified</span>
                                </div>
                                {s.verified ? (
                                  <div className="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-green-50 text-green-700 border border-green-200">
                                    <CheckCircle className="h-2.5 w-2.5 mr-0.5" />
                                    Verified
                                  </div>
                                ) : (
                                  <div className="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-gray-50 text-gray-500 border border-gray-200">
                                    <XCircle className="h-2.5 w-2.5 mr-0.5" />
                                    Belum
                                  </div>
                                )}
                              </div>
                            </div>
                          </div>
                        </div>
                        
                        <div className="flex justify-end gap-1 mt-3">
                          <button onClick={() => setSelected(s)} className="p-2 bg-blue-50 text-blue-600 rounded-lg shadow-sm hover:bg-blue-100 transition-colors" title="Detail">
                            <Eye className="h-4 w-4" />
                          </button>
                          <button onClick={() => handleEdit(s)} className="p-2 bg-yellow-50 text-yellow-600 rounded-lg shadow-sm hover:bg-yellow-100 transition-colors" title="Edit">
                            <Edit className="h-4 w-4" />
                          </button>
                          <button onClick={() => generatePDFIdentitas(s)} className="p-2 bg-purple-50 text-purple-600 rounded-lg shadow-sm hover:bg-purple-100 transition-colors" title="PDF">
                            <Download className="h-4 w-4" />
                          </button>
                          <button onClick={() => handleDelete(s)} className="p-2 bg-red-50 text-red-600 rounded-lg shadow-sm hover:bg-red-100 transition-colors" title="Hapus">
                            <Trash2 className="h-4 w-4" />
                          </button>
                        </div>
                      </div>
                    ))}
                  </div>
                </div>
              )}
              {/* Pagination baru */}
              {studentTotalItems > 0 && (
                <div className="px-6 py-4 border-t border-gray-200 bg-gray-50">
                  <Pagination
                    currentPage={studentPage}
                    totalPages={studentTotalPages}
                    totalItems={studentTotalItems}
                    itemsPerPage={studentPageSize}
                    onPageChange={setStudentPage}
                    onItemsPerPageChange={setStudentPageSize}
                  />
                </div>
              )}
            </div>
          </>
        )}
        {tab === 'absen' && (
          <div className="bg-white shadow-sm rounded-xl border border-gray-200 overflow-hidden">
            {/* Desktop Table */}
            <div className="overflow-x-auto hidden md:block">
              <table className="min-w-full">
                <thead className="bg-gray-50 border-b border-gray-200">
                  <tr>
                    <th className="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                      <div className="flex items-center gap-2">
                        <User className="h-4 w-4 text-gray-500" />
                        Nama Siswa
                      </div>
                    </th>
                    {filteredAttendanceSessions.map((session, index) => (
                      <th key={session.id} className="px-4 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider min-w-[120px]">
                        <div className="flex flex-col items-center gap-1">
                          <span className="text-gray-700 font-medium">
                            {new Date(session.session_date).toLocaleDateString('id-ID', { 
                              day: 'numeric', 
                              month: 'short' 
                            })}
                          </span>
                          <span className="text-xs text-gray-500">
                            {new Date(session.session_date).toLocaleDateString('id-ID', { 
                              weekday: 'short' 
                            })}
                          </span>
                        </div>
                      </th>
                    ))}
                  </tr>
                </thead>
                <tbody className="bg-white divide-y divide-gray-100">
                  {absenPaged.map((s, studentIndex) => (
                    <tr key={s.id} className="hover:bg-gray-50 transition-colors">
                      <td className="px-6 py-4 border-r border-gray-200">
                        <div className="flex items-center gap-3">
                          <div className="w-8 h-8 bg-gradient-to-r from-blue-400 to-blue-600 rounded-full flex items-center justify-center text-white text-sm font-semibold">
                            {(absenPage - 1) * absenPageSize + studentIndex + 1}
                          </div>
                          <div>
                            <div className="font-semibold text-gray-900 text-sm">{s.full_name}</div>
                            <div className="text-xs text-gray-500">NIS: {s.nis || 'Tidak ada data'}</div>
                          </div>
                        </div>
                      </td>
                      {filteredAttendanceSessions.map(session => {
                        const rec = attendanceRecords.find(r => 
                          r.attendance_session_id === session.id && r.student_id === s.id
                        );
                        
                        let statusConfig: {
                            bg: string;
                            text: string;
                            border: string;
                            label: string;
                            icon: string | null;
                          } = {
                            bg: 'bg-gray-100',
                            text: 'text-gray-500',
                            border: 'border-gray-200',
                            label: '-',
                            icon: null
                          };
                        
                        if (rec) {
                          switch (rec.status) {
                            case 'hadir':
                              statusConfig = {
                                bg: 'bg-green-50',
                                text: 'text-green-700',
                                border: 'border-green-200',
                                label: 'Hadir',
                                icon: '✓'
                              };
                              break;
                            case 'sakit':
                              statusConfig = {
                                bg: 'bg-yellow-50',
                                text: 'text-yellow-700',
                                border: 'border-yellow-200',
                                label: 'Sakit',
                                icon: '🏥'
                              };
                              break;
                            case 'izin':
                              statusConfig = {
                                bg: 'bg-blue-50',
                                text: 'text-blue-700',
                                border: 'border-blue-200',
                                label: 'Izin',
                                icon: '📝'
                              };
                              break;
                            case 'alpha':
                              statusConfig = {
                                bg: 'bg-red-50',
                                text: 'text-red-700',
                                border: 'border-red-200',
                                label: 'Alpha',
                                icon: '❌'
                              };
                              break;
                          }
                        }
                        
                        return (
                          <td key={session.id} className="px-4 py-4 text-center">
                            <div className="flex flex-col items-center gap-1">
                              <div className={`inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold border ${statusConfig.bg} ${statusConfig.text} ${statusConfig.border} cursor-pointer hover:shadow-md transition-all`}
                                   onClick={() => handleEditAttendance(s, session, rec?.status || 'alpha')}>
                                {statusConfig.label}
                              </div>
                              <button 
                                onClick={() => handleEditAttendance(s, session, rec?.status || 'alpha')}
                                className="text-xs text-blue-600 hover:text-blue-800 font-medium"
                              >
                                Edit
                              </button>
                            </div>
                          </td>
                        );
                      })}
                    </tr>
                  ))}
                </tbody>
              </table>
              {/* Pagination Desktop */}
              {absenTotalItems > 0 && (
                <div className="px-6 py-4 border-t border-gray-200 bg-gray-50">
                  <Pagination
                    currentPage={absenPage}
                    totalPages={absenTotalPages}
                    totalItems={absenTotalItems}
                    itemsPerPage={absenPageSize}
                    onPageChange={setAbsenPage}
                    onItemsPerPageChange={setAbsenPageSize}
                  />
                </div>
              )}
            </div>
            {/* Mobile Card List for Rekap Kehadiran */}
            {!attendanceLoading && filteredAttendanceSessions.length > 0 && (
              <div className="md:hidden p-2">
                <div className="space-y-2">
                  {absenPaged.map((s, studentIndex) => {
                    return (
                      <div key={s.id} className="bg-white rounded-xl shadow-sm border border-gray-100 p-3">
                        <div className="flex items-center gap-3 mb-3">
                          <div className="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center text-white text-xs font-bold shadow-sm">
                            {(absenPage - 1) * absenPageSize + studentIndex + 1}
                          </div>
                          <div className="flex-1 min-w-0">
                            <div className="font-semibold text-gray-900 text-sm truncate">{s.full_name}</div>
                            <div className="text-xs text-gray-500 flex items-center gap-1">
                              <User className="h-3 w-3" />
                              NIS: {s.nis || 'Tidak ada data'}
                            </div>
                          </div>
                        </div>
                        <div className="bg-gray-50 rounded-lg p-2">
                          <div className="text-xs font-medium text-gray-600 mb-2 flex items-center gap-1">
                            <Calendar className="h-3 w-3" />
                            Kehadiran ({filteredAttendanceSessions.length} hari)
                          </div>
                          <div className="grid grid-cols-4 gap-1">
                            {filteredAttendanceSessions.map(session => {
                              const rec = attendanceRecords.find(r => 
                                r.attendance_session_id === session.id && r.student_id === s.id
                              );
                              let statusConfig = {
                                bg: 'bg-gray-100',
                                text: 'text-gray-500',
                                border: 'border-gray-200',
                                label: '-',
                                // icon: null
                              };
                              if (rec) {
                                switch (rec.status) {
                                  case 'hadir':
                                    statusConfig = {
                                      bg: 'bg-green-100',
                                      text: 'text-green-700',
                                      border: 'border-green-200',
                                      label: 'Hadir',
                                      // icon: '✓'
                                    };
                                    break;
                                  case 'sakit':
                                    statusConfig = {
                                      bg: 'bg-yellow-100',
                                      text: 'text-yellow-700',
                                      border: 'border-yellow-200',
                                      label: 'Sakit',
                                      // icon: '🏥'
                                    };
                                    break;
                                  case 'izin':
                                    statusConfig = {
                                      bg: 'bg-blue-100',
                                      text: 'text-blue-700',
                                      border: 'border-blue-200',
                                      label: 'Izin',
                                      // icon: '📝'
                                    };
                                    break;
                                  case 'alpha':
                                    statusConfig = {
                                      bg: 'bg-red-100',
                                      text: 'text-red-700',
                                      border: 'border-red-200',
                                      label: 'Alpha',
                                      // icon: '❌'
                                    };
                                    break;
                                }
                              }
                              return (
                                <div key={session.id} className="text-center">
                                  <div className="flex flex-col items-center gap-1">
                                    <div className={`inline-flex items-center justify-center px-2 py-1 rounded-md text-xs font-bold border ${statusConfig.bg} ${statusConfig.text} ${statusConfig.border} shadow-sm cursor-pointer hover:shadow-md transition-all`}
                                         onClick={() => handleEditAttendance(s, session, rec?.status || 'alpha')}>
                                      {statusConfig.label}
                                    </div>
                                    <button 
                                      onClick={() => handleEditAttendance(s, session, rec?.status || 'alpha')}
                                      className="text-xs text-blue-600 hover:text-blue-800 font-medium"
                                    >
                                      Edit
                                    </button>
                                    <div className="text-xs text-gray-500 font-medium">
                                      {new Date(session.session_date).toLocaleDateString('id-ID', { 
                                        day: 'numeric', 
                                        month: 'short' 
                                      })}
                                    </div>
                                  </div>
                                </div>
                              );
                            })}
                          </div>
                        </div>
                      </div>
                    );
                  })}
                </div>
                {/* Pagination Mobile */}
                {absenTotalItems > 0 && (
                  <div className="px-6 py-4 border-t border-gray-200 bg-gray-50">
                    <Pagination
                      currentPage={absenPage}
                      totalPages={absenTotalPages}
                      totalItems={absenTotalItems}
                      itemsPerPage={absenPageSize}
                      onPageChange={setAbsenPage}
                      onItemsPerPageChange={setAbsenPageSize}
                    />
                  </div>
                )}
              </div>
            )}
            {/* Summary Stats */}
            {filteredAttendanceSessions.length > 0 && (
              <div className="px-6 py-4 bg-gray-50 border-t border-gray-200">
                <div className="flex flex-wrap items-center justify-between gap-4">
                  <div className="flex items-center gap-6 text-sm">
                    <div className="flex items-center gap-2">
                      <div className="w-3 h-3 bg-green-500 rounded-full"></div>
                      <span className="text-gray-600">Hadir</span>
                    </div>
                    <div className="flex items-center gap-2">
                      <div className="w-3 h-3 bg-yellow-500 rounded-full"></div>
                      <span className="text-gray-600">Sakit</span>
                    </div>
                    <div className="flex items-center gap-2">
                      <div className="w-3 h-3 bg-blue-500 rounded-full"></div>
                      <span className="text-gray-600">Izin</span>
                    </div>
                    <div className="flex items-center gap-2">
                      <div className="w-3 h-3 bg-red-500 rounded-full"></div>
                      <span className="text-gray-600">Alpha</span>
                    </div>
                  </div>
                  <div className="text-sm text-gray-500">
                    Total {filteredAttendanceSessions.length} hari absen
                    {dateFilter.start || dateFilter.end ? ' (filtered)' : ''}
                  </div>
                </div>
              </div>
            )}
          </div>
        )}
        {tab === 'rekap-siswa' && (
          <div className="bg-white shadow-sm rounded-xl border border-gray-200 overflow-hidden">
            <div className="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-green-50 to-emerald-50">
              <h3 className="text-lg font-semibold text-green-700 flex items-center gap-2">
                <Users className="h-5 w-5" />
                Rekap Kehadiran per Siswa
              </h3>
              <p className="text-sm text-gray-600 mt-1">
                Ringkasan kehadiran setiap siswa dalam periode tertentu
              </p>
            </div>

            {/* Date Filter */}
            <div className="px-6 py-4 border-b border-gray-200 bg-gray-50">
              <div className="flex flex-col sm:flex-row sm:items-center gap-4">
                <div className="flex items-center gap-2">
                  <Calendar className="h-4 w-4 text-gray-600" />
                  <span className="text-sm font-medium text-gray-700">Filter Tanggal:</span>
                </div>
                <div className="flex flex-col sm:flex-row gap-3">
                  <div className="flex items-center gap-2">
                    <label className="text-xs text-gray-600 whitespace-nowrap">Dari:</label>
                    <input
                      type="date"
                      value={dateFilter.start}
                      onChange={(e) => setDateFilter(prev => ({ ...prev, start: e.target.value }))}
                      className="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500"
                    />
                  </div>
                  <div className="flex items-center gap-2">
                    <label className="text-xs text-gray-600 whitespace-nowrap">Sampai:</label>
                    <input
                      type="date"
                      value={dateFilter.end}
                      onChange={(e) => setDateFilter(prev => ({ ...prev, end: e.target.value }))}
                      className="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500"
                    />
                  </div>
                  <button
                    onClick={() => setDateFilter({start: '', end: ''})}
                    className="px-3 py-2 bg-gray-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-300 transition-colors"
                  >
                    Reset
                  </button>
                </div>
                {dateFilter.start || dateFilter.end ? (
                  <div className="text-xs text-gray-500">
                    Menampilkan data dari {filteredAttendanceSessions.length} hari
                  </div>
                ) : null}
              </div>
            </div>

            {attendanceLoading ? (
              <div className="p-8 text-center">
                <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-green-600 mx-auto mb-3"></div>
                <p className="text-gray-600">Memuat data kehadiran...</p>
              </div>
            ) : filteredAttendanceSessions.length === 0 ? (
              <div className="p-8 text-center">
                <div className="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                  <Users className="h-8 w-8 text-gray-400" />
                </div>
                <h4 className="text-lg font-medium text-gray-700 mb-2">
                  {attendanceSessions.length === 0 ? 'Belum Ada Data Absen' : 'Tidak Ada Data untuk Rentang Tanggal Ini'}
                </h4>
                <p className="text-gray-500 text-sm">
                  {attendanceSessions.length === 0 
                    ? 'Data kehadiran akan muncul setelah ada absen yang disimpan.'
                    : 'Coba ubah filter tanggal atau reset filter.'
                  }
                </p>
              </div>
            ) : (
              <>
                {/* Desktop Table */}
                <div className="overflow-x-auto hidden md:block">
                  <table className="min-w-full">
                    <thead className="bg-gray-50 border-b border-gray-200">
                      <tr>
                        <th className="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                          <div className="flex items-center gap-2">
                            <User className="h-4 w-4 text-gray-500" />
                            Nama Siswa
                          </div>
                        </th>
                        <th className="px-4 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                          <div className="flex flex-col items-center gap-1">
                            <span className="text-green-700 font-medium">Hadir</span>
                            <span className="text-xs text-gray-500">Jumlah</span>
                          </div>
                        </th>
                        <th className="px-4 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                          <div className="flex flex-col items-center gap-1">
                            <span className="text-yellow-700 font-medium">Sakit</span>
                            <span className="text-xs text-gray-500">Jumlah</span>
                          </div>
                        </th>
                        <th className="px-4 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                          <div className="flex flex-col items-center gap-1">
                            <span className="text-blue-700 font-medium">Izin</span>
                            <span className="text-xs text-gray-500">Jumlah</span>
                          </div>
                        </th>
                        <th className="px-4 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                          <div className="flex flex-col items-center gap-1">
                            <span className="text-red-700 font-medium">Alpha</span>
                            <span className="text-xs text-gray-500">Jumlah</span>
                          </div>
                        </th>
                        <th className="px-4 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                          <div className="flex flex-col items-center gap-1">
                            <span className="text-gray-700 font-medium">Total</span>
                            <span className="text-xs text-gray-500">Hari</span>
                          </div>
                        </th>
                        <th className="px-4 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                          <div className="flex flex-col items-center gap-1">
                            <span className="text-gray-700 font-medium">%</span>
                            <span className="text-xs text-gray-500">Hadir</span>
                          </div>
                        </th>
                      </tr>
                    </thead>
                    <tbody className="bg-white divide-y divide-gray-100">
                      {rekapPaged.map((s, studentIndex) => {
                        // Calculate attendance stats for this student
                        const studentRecords = attendanceRecords.filter(r => 
                          r.student_id === s.id && 
                          filteredAttendanceSessions.some(session => session.id === r.attendance_session_id)
                        );
                        const hadirCount = studentRecords.filter(r => r.status === 'hadir').length;
                        const sakitCount = studentRecords.filter(r => r.status === 'sakit').length;
                        const izinCount = studentRecords.filter(r => r.status === 'izin').length;
                        const alphaCount = studentRecords.filter(r => r.status === 'alpha').length;
                        const totalDays = filteredAttendanceSessions.length;
                        const hadirPercent = totalDays > 0 ? ((hadirCount / totalDays) * 100).toFixed(1) : '0';
                        return (
                          <tr key={s.id} className="hover:bg-gray-50 transition-colors">
                            <td className="px-6 py-4 border-r border-gray-200">
                              <div className="flex items-center gap-3">
                                <div className="w-8 h-8 bg-gradient-to-r from-green-400 to-green-600 rounded-full flex items-center justify-center text-white text-sm font-semibold">
                                  {(rekapPage - 1) * rekapPageSize + studentIndex + 1}
                                </div>
                                <div>
                                  <div className="font-semibold text-gray-900 text-sm">{s.full_name}</div>
                                  <div className="text-xs text-gray-500">NIS: {s.nis || 'Tidak ada data'}</div>
                                </div>
                              </div>
                            </td>
                            <td className="px-4 py-4 text-center">
                              <div className="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-50 text-green-700 border border-green-200">
                                {hadirCount}
                              </div>
                            </td>
                            <td className="px-4 py-4 text-center">
                              <div className="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-yellow-50 text-yellow-700 border border-yellow-200">
                                {sakitCount}
                              </div>
                            </td>
                            <td className="px-4 py-4 text-center">
                              <div className="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                {izinCount}
                              </div>
                            </td>
                            <td className="px-4 py-4 text-center">
                              <div className="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-red-50 text-red-700 border border-red-200">
                                {alphaCount}
                              </div>
                            </td>
                            <td className="px-4 py-4 text-center">
                              <div className="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-50 text-gray-700 border border-gray-200">
                                {totalDays}
                              </div>
                            </td>
                            <td className="px-4 py-4 text-center">
                              <div className={`inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold border ${
                                parseFloat(hadirPercent) >= 80 ? 'bg-green-50 text-green-700 border-green-200' :
                                parseFloat(hadirPercent) >= 60 ? 'bg-yellow-50 text-yellow-700 border-yellow-200' :
                                'bg-red-50 text-red-700 border-red-200'
                              }`}>
                                {hadirPercent}%
                              </div>
                            </td>
                          </tr>
                        );
                      })}
                    </tbody>
                  </table>
                  {/* Pagination Desktop */}
                  {rekapTotalItems > 0 && (
                    <div className="px-6 py-4 border-t border-gray-200 bg-gray-50">
                      <Pagination
                        currentPage={rekapPage}
                        totalPages={rekapTotalPages}
                        totalItems={rekapTotalItems}
                        itemsPerPage={rekapPageSize}
                        onPageChange={setRekapPage}
                        onItemsPerPageChange={setRekapPageSize}
                      />
                    </div>
                  )}
                </div>
                {/* Mobile Card List */}
                {!attendanceLoading && filteredAttendanceSessions.length > 0 && (
                  <div className="md:hidden p-2">
                    <div className="space-y-2">
                      {rekapPaged.map((s, studentIndex) => {
                        // Calculate attendance stats for this student
                        const studentRecords = attendanceRecords.filter(r => 
                          r.student_id === s.id && 
                          filteredAttendanceSessions.some(session => session.id === r.attendance_session_id)
                        );
                        const hadirCount = studentRecords.filter(r => r.status === 'hadir').length;
                        const sakitCount = studentRecords.filter(r => r.status === 'sakit').length;
                        const izinCount = studentRecords.filter(r => r.status === 'izin').length;
                        const alphaCount = studentRecords.filter(r => r.status === 'alpha').length;
                        const totalDays = filteredAttendanceSessions.length;
                        const hadirPercent = totalDays > 0 ? ((hadirCount / totalDays) * 100).toFixed(1) : '0';
                        return (
                          <div key={s.id} className="bg-white rounded-xl shadow-sm border border-gray-100 p-3">
                            <div className="flex items-center gap-3 mb-3">
                              <div className="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center text-white text-xs font-bold shadow-sm">
                                {(rekapPage - 1) * rekapPageSize + studentIndex + 1}
                              </div>
                              <div className="flex-1 min-w-0">
                                <div className="font-semibold text-gray-900 text-sm truncate">{s.full_name}</div>
                                <div className="text-xs text-gray-500 flex items-center gap-1">
                                  <User className="h-3 w-3" />
                                  NIS: {s.nis || 'Tidak ada data'}
                                </div>
                              </div>
                            </div>
                            <div className="bg-gradient-to-r from-gray-50 to-gray-100 rounded-lg p-3">
                              <div className="text-xs font-medium text-gray-600 mb-3 flex items-center gap-1">
                                <BarChart3 className="h-3 w-3" />
                                Statistik Kehadiran
                              </div>
                              <div className="grid grid-cols-2 gap-2 mb-3">
                                <div className="bg-white rounded-lg p-2 border border-green-200">
                                  <div className="flex items-center justify-between">
                                    <div className="flex items-center gap-2">
                                      <div className="w-2 h-2 bg-green-500 rounded-full"></div>
                                      <span className="text-xs font-medium text-green-700">Hadir</span>
                                    </div>
                                    <span className="text-sm font-bold text-green-700">{hadirCount}</span>
                                  </div>
                                </div>
                                <div className="bg-white rounded-lg p-2 border border-yellow-200">
                                  <div className="flex items-center justify-between">
                                    <div className="flex items-center gap-2">
                                      <div className="w-2 h-2 bg-yellow-500 rounded-full"></div>
                                      <span className="text-xs font-medium text-yellow-700">Sakit</span>
                                    </div>
                                    <span className="text-sm font-bold text-yellow-700">{sakitCount}</span>
                                  </div>
                                </div>
                                <div className="bg-white rounded-lg p-2 border border-blue-200">
                                  <div className="flex items-center justify-between">
                                    <div className="flex items-center gap-2">
                                      <div className="w-2 h-2 bg-blue-500 rounded-full"></div>
                                      <span className="text-xs font-medium text-blue-700">Izin</span>
                                    </div>
                                    <span className="text-sm font-bold text-blue-700">{izinCount}</span>
                                  </div>
                                </div>
                                <div className="bg-white rounded-lg p-2 border border-red-200">
                                  <div className="flex items-center justify-between">
                                    <div className="flex items-center gap-2">
                                      <div className="w-2 h-2 bg-red-500 rounded-full"></div>
                                      <span className="text-xs font-medium text-red-700">Alpha</span>
                                    </div>
                                    <span className="text-sm font-bold text-red-700">{alphaCount}</span>
                                  </div>
                                </div>
                              </div>
                              <div className="flex justify-between items-center pt-2 border-t border-gray-200">
                                <div className="flex items-center gap-2">
                                  <div className="w-6 h-6 bg-gray-100 rounded-lg flex items-center justify-center">
                                    <Calendar className="h-3 w-3 text-gray-600" />
                                  </div>
                                  <div>
                                    <div className="text-xs text-gray-500">Total Hari</div>
                                    <div className="text-sm font-bold text-gray-700">{totalDays}</div>
                                  </div>
                                </div>
                                <div className="flex items-center gap-2">
                                  <div className={`w-6 h-6 rounded-lg flex items-center justify-center ${
                                    parseFloat(hadirPercent) >= 80 ? 'bg-green-100' :
                                    parseFloat(hadirPercent) >= 60 ? 'bg-yellow-100' :
                                    'bg-red-100'
                                  }`}>
                                    <Percent className={`h-3 w-3 ${
                                      parseFloat(hadirPercent) >= 80 ? 'text-green-600' :
                                      parseFloat(hadirPercent) >= 60 ? 'text-yellow-600' :
                                      'text-red-600'
                                    }`} />
                                  </div>
                                  <div>
                                    <div className={`text-sm font-bold ${
                                      parseFloat(hadirPercent) >= 80 ? 'text-green-700' :
                                      parseFloat(hadirPercent) >= 60 ? 'text-yellow-700' :
                                      'text-red-700'
                                    }`}>
                                      {hadirPercent}%
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        );
                      })}
                    </div>
                    {/* Pagination Mobile */}
                    {rekapTotalItems > 0 && (
                      <div className="px-6 py-4 border-t border-gray-200 bg-gray-50">
                        <Pagination
                          currentPage={rekapPage}
                          totalPages={rekapTotalPages}
                          totalItems={rekapTotalItems}
                          itemsPerPage={rekapPageSize}
                          onPageChange={setRekapPage}
                          onItemsPerPageChange={setRekapPageSize}
                        />
                      </div>
                    )}
                  </div>
                )}
              </>
            )}
            {/* Summary Stats */}
            {filteredAttendanceSessions.length > 0 && (
              <div className="px-6 py-4 bg-gray-50 border-t border-gray-200">
                <div className="flex flex-wrap items-center justify-between gap-4">
                  <div className="flex items-center gap-6 text-sm">
                    <div className="flex items-center gap-2">
                      <div className="w-3 h-3 bg-green-500 rounded-full"></div>
                      <span className="text-gray-600">Hadir</span>
                    </div>
                    <div className="flex items-center gap-2">
                      <div className="w-3 h-3 bg-yellow-500 rounded-full"></div>
                      <span className="text-gray-600">Sakit</span>
                    </div>
                    <div className="flex items-center gap-2">
                      <div className="w-3 h-3 bg-blue-500 rounded-full"></div>
                      <span className="text-gray-600">Izin</span>
                    </div>
                    <div className="flex items-center gap-2">
                      <div className="w-3 h-3 bg-red-500 rounded-full"></div>
                      <span className="text-gray-600">Alpha</span>
                    </div>
                  </div>
                  <div className="text-sm text-gray-500">
                    Total {filteredAttendanceSessions.length} hari absen
                    {dateFilter.start || dateFilter.end ? ' (filtered)' : ''}
                  </div>
                </div>
              </div>
            )}
          </div>
        )}

        {/* Detail Modal */}
        <StudentDetailModal student={selected} onClose={() => setSelected(null)} />
        
        {/* Edit Modal */}
        <StudentEditModal student={editModal.student} open={editModal.open} onClose={() => setEditModal({open: false, student: null})} onSave={saveEdit} />
        
        {/* Delete Modal */}
        <ConfirmDeleteModal open={deleteModal.open} onClose={() => setDeleteModal({open: false, student: null})} onDelete={confirmDelete} />

        {/* Edit Attendance Modal - Outside of tab conditions */}
        {editAttendanceModal.open && (
          <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
            <div className="bg-white rounded-lg shadow-xl max-w-md w-full">
              <div className="p-6">
                <div className="flex items-center mb-4">
                  <div className="p-2 bg-blue-100 rounded-lg mr-3">
                    <Edit className="h-6 w-6 text-blue-600" />
                  </div>
                  <div>
                    <h3 className="text-lg font-semibold text-gray-900">Edit Status Kehadiran</h3>
                    <p className="text-sm text-gray-600">
                      {editAttendanceModal.student?.full_name} - {editAttendanceModal.session && new Date(editAttendanceModal.session.session_date).toLocaleDateString('id-ID', {
                        weekday: 'long',
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric'
                      })}
                    </p>
                  </div>
                </div>
                
                <div className="mb-4">
                  <label className="block text-sm font-medium text-gray-700 mb-3">
                    Status Kehadiran Saat Ini: 
                    <span className={`ml-2 px-2 py-1 rounded text-xs font-semibold ${
                      editAttendanceModal.currentStatus === 'hadir' ? 'bg-green-100 text-green-700' :
                      editAttendanceModal.currentStatus === 'sakit' ? 'bg-yellow-100 text-yellow-700' :
                      editAttendanceModal.currentStatus === 'izin' ? 'bg-blue-100 text-blue-700' :
                      'bg-red-100 text-red-700'
                    }`}>
                      {editAttendanceModal.currentStatus === 'hadir' ? 'Hadir' :
                       editAttendanceModal.currentStatus === 'sakit' ? 'Sakit' :
                       editAttendanceModal.currentStatus === 'izin' ? 'Izin' : 'Alpha'}
                    </span>
                  </label>
                  
                  <div className="grid grid-cols-2 gap-3">
                    <button
                      onClick={() => saveAttendanceEdit('hadir')}
                      className="flex items-center justify-center p-3 border border-green-300 rounded-lg hover:bg-green-50 transition-colors"
                    >
                      <Check className="h-5 w-5 text-green-600 mr-2" />
                      <span className="text-sm font-medium text-green-700">Hadir</span>
                    </button>
                    
                    <button
                      onClick={() => saveAttendanceEdit('sakit')}
                      className="flex items-center justify-center p-3 border border-yellow-300 rounded-lg hover:bg-yellow-50 transition-colors"
                    >
                      <AlertCircle className="h-5 w-5 text-yellow-600 mr-2" />
                      <span className="text-sm font-medium text-yellow-700">Sakit</span>
                    </button>
                    
                    <button
                      onClick={() => saveAttendanceEdit('izin')}
                      className="flex items-center justify-center p-3 border border-blue-300 rounded-lg hover:bg-blue-50 transition-colors"
                    >
                      <Clock className="h-5 w-5 text-blue-600 mr-2" />
                      <span className="text-sm font-medium text-blue-700">Izin</span>
                    </button>
                    
                    <button
                      onClick={() => saveAttendanceEdit('alpha')}
                      className="flex items-center justify-center p-3 border border-red-300 rounded-lg hover:bg-red-50 transition-colors"
                    >
                      <X className="h-5 w-5 text-red-600 mr-2" />
                      <span className="text-sm font-medium text-red-700">Alpha</span>
                    </button>
                  </div>
                </div>
                
                <div className="flex justify-end">
                  <button
                    onClick={() => setEditAttendanceModal({open: false, student: null, session: null, currentStatus: ''})}
                    className="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors"
                  >
                    Batal
                  </button>
                </div>
              </div>
            </div>
          </div>
        )}
      </div>
    </div>
  );
} 