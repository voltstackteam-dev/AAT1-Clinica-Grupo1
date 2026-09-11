import { Component, OnInit, inject, signal } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { HttpClient } from '@angular/common/http';
import { AuthService } from '../../services/auth.service';

@Component({ selector: 'app-admin-dashboard', standalone: true, imports: [CommonModule, FormsModule], templateUrl: './admin-dashboard.html', styleUrl: './admin-dashboard.css' })
export class AdminDashboardComponent implements OnInit {
  private http = inject(HttpClient); private auth = inject(AuthService); private api = 'http://localhost:8000/api';
  citas = signal<any[]>([]); horarios = signal<any[]>([]); medicos = signal<any[]>([]); filtroActual = signal('TODAS'); vistaActiva = signal<'citas' | 'horarios'>('citas');
  cargando = signal(false); mensaje = signal(''); actualizandoCita = signal<number | null>(null); editandoCita = signal<number | null>(null); editandoHorario = signal<number | null>(null); medicoActual = signal(0);
  diaHorario = signal('LUNES'); inicioHorario = signal('08:00'); finHorario = signal('17:00'); fechaNueva = signal(''); horaNueva = signal('');
  esMedico = false; esAdministrador = false; hoy = new Date().toISOString().slice(0, 10); horas = Array.from({ length: 9 }, (_, indice) => `${String(indice + 8).padStart(2, '0')}:00`);

  ngOnInit(): void {
    const usuario = this.auth.obtenerUsuario(); if (!usuario) return;
    this.esMedico = Number(usuario.id_rol) === 2; this.esAdministrador = Number(usuario.id_rol) === 1;
    if (!this.esMedico && !this.esAdministrador) { this.mensaje.set('No tiene permisos para acceder a este panel.'); return; }
    if (this.esAdministrador) { this.cargarCitas(); this.cargarMedicos(); return; }
    this.http.get<any>(`${this.api}/medicos.php`).subscribe({ next: respuesta => { const medico = (respuesta.data || []).find((item: any) => Number(item.id_usuario) === Number(usuario.id_usuario)); if (!medico) { this.mensaje.set('No hay un perfil médico vinculado a esta cuenta.'); return; } this.medicoActual.set(Number(medico.id_medico)); this.cargarCitas(); this.cargarHorarios(); }, error: () => this.mensaje.set('No se pudo cargar el perfil del médico.') });
  }

  cargarCitas(): void { this.cargando.set(true); const url = this.esMedico ? `${this.api}/citas.php?id_medico=${this.medicoActual()}` : `${this.api}/citas.php`; this.http.get<any>(url).subscribe({ next: respuesta => { this.citas.set(respuesta.data || []); this.cargando.set(false); }, error: () => { this.mensaje.set('No se pudieron cargar las citas.'); this.cargando.set(false); } }); }
  cargarMedicos(): void { this.http.get<any>(`${this.api}/medicos.php`).subscribe({ next: respuesta => this.medicos.set(respuesta.data || []), error: () => this.mensaje.set('No se pudieron cargar los médicos.') }); }
  cargarHorarios(): void { if (!this.medicoActual()) { this.horarios.set([]); return; } this.http.get<any>(`${this.api}/horarios.php?id_medico=${this.medicoActual()}`).subscribe({ next: respuesta => this.horarios.set(respuesta.data || []), error: () => this.mensaje.set('No se pudieron cargar los horarios.') }); }
  cambiarVista(vista: 'citas' | 'horarios'): void { this.vistaActiva.set(vista); this.mensaje.set(''); if (vista === 'horarios') this.cargarHorarios(); }
  seleccionarMedico(id: string): void { this.medicoActual.set(Number(id)); this.cargarHorarios(); }
  citasFiltradas(): any[] { return this.filtroActual() === 'TODAS' ? this.citas() : this.citas().filter(cita => cita.estado === this.filtroActual()); }
  setFiltro(filtro: string): void { this.filtroActual.set(filtro); }
  puedeConfirmar(cita: any): boolean { return cita.estado === 'PENDIENTE' && (this.esMedico || this.esAdministrador); }
  puedeCancelar(cita: any): boolean { return this.esAdministrador ? ['PENDIENTE', 'CONFIRMADA'].includes(cita.estado) : this.esMedico && cita.estado === 'PENDIENTE'; }
  puedeCompletar(cita: any): boolean { return this.esAdministrador && cita.estado === 'CONFIRMADA'; }
  puedeReprogramar(cita: any): boolean { return (this.esMedico || this.esAdministrador) && ['PENDIENTE', 'CONFIRMADA'].includes(cita.estado); }
  actualizarEstado(cita: any, estado: string): void { if (estado === 'CANCELADA' && !confirm(`¿Desea rechazar o cancelar la cita #${cita.id_cita}?`)) return; this.actualizandoCita.set(cita.id_cita); this.http.put<any>(`${this.api}/citas.php`, { id_cita: cita.id_cita, estado }).subscribe({ next: respuesta => { this.citas.update(citas => citas.map(item => item.id_cita === cita.id_cita ? { ...item, estado: respuesta.estado } : item)); this.actualizandoCita.set(null); }, error: error => { this.mensaje.set(error.error?.mensaje || 'No se pudo actualizar el estado de la cita.'); this.actualizandoCita.set(null); } }); }
  prepararReprogramacion(cita: any): void { const [fecha, hora] = String(cita.fecha_hora).split(' '); this.editandoCita.set(cita.id_cita); this.fechaNueva.set(fecha || ''); this.horaNueva.set((hora || '').slice(0, 5)); }
  reprogramar(cita: any): void { if (!this.fechaNueva() || !this.horaNueva()) { this.mensaje.set('Selecciona fecha y hora para reprogramar la cita.'); return; } this.actualizandoCita.set(cita.id_cita); this.http.put<any>(`${this.api}/citas.php`, { id_cita: cita.id_cita, fecha_hora: `${this.fechaNueva()}T${this.horaNueva()}` }).subscribe({ next: respuesta => { this.citas.update(citas => citas.map(item => item.id_cita === cita.id_cita ? { ...item, estado: respuesta.estado, fecha_hora: respuesta.fecha_hora } : item)); this.editandoCita.set(null); this.actualizandoCita.set(null); this.mensaje.set(respuesta.mensaje); }, error: error => { this.mensaje.set(error.error?.mensaje || 'No se pudo reprogramar la cita.'); this.actualizandoCita.set(null); } }); }
  guardarHorario(): void { if (!this.medicoActual()) { this.mensaje.set('Selecciona un médico antes de agregar un horario.'); return; } const datos = { id_medico: this.medicoActual(), dia_semana: this.diaHorario(), hora_inicio: this.inicioHorario(), hora_fin: this.finHorario() }; const id = this.editandoHorario(); const solicitud = id ? this.http.put<any>(`${this.api}/horarios.php?id=${id}`, datos) : this.http.post<any>(`${this.api}/horarios.php`, datos); solicitud.subscribe({ next: () => { this.mensaje.set(id ? 'Horario actualizado correctamente.' : 'Horario guardado correctamente.'); this.editandoHorario.set(null); this.cargarHorarios(); }, error: error => this.mensaje.set(error.error?.mensaje || 'No se pudo guardar el horario.') }); }
  editarHorario(horario: any): void { this.editandoHorario.set(horario.id_disponibilidad); this.diaHorario.set(horario.dia_semana); this.inicioHorario.set(horario.hora_inicio.slice(0, 5)); this.finHorario.set(horario.hora_fin.slice(0, 5)); }
  eliminarHorario(horario: any): void { if (!confirm(`¿Desea eliminar el horario del ${horario.dia_semana}?`)) return; this.http.delete<any>(`${this.api}/horarios.php?id=${horario.id_disponibilidad}`).subscribe({ next: () => { this.mensaje.set('Horario eliminado.'); this.cargarHorarios(); }, error: error => this.mensaje.set(error.error?.mensaje || 'No se pudo eliminar el horario.') }); }
}
