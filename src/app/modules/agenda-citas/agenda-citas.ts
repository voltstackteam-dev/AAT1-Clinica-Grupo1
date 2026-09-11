import { Component, OnInit, inject, signal } from '@angular/core';
import { CommonModule } from '@angular/common'; import { FormsModule } from '@angular/forms'; 
import { HttpClient } from '@angular/common/http';
import { Router } from '@angular/router';
import { AuthService } from '../../services/auth.service';


@Component({ selector: 'app-agenda-citas', standalone: true, imports: [CommonModule, FormsModule], templateUrl: './agenda-citas.html', styleUrl: './agenda-citas.css' })


export class AgendaCitasComponent implements OnInit {
    private http = inject(HttpClient); private auth = inject(AuthService); private router = inject(Router); private api = 'http://localhost:8000/api'; hoy = new Date().toISOString().slice(0, 10);
    idMedico = signal(0); idSala = signal(0); fecha = signal(''); hora = signal(''); motivoConsulta = signal(''); medicos = signal<any[]>([]); salas = signal<any[]>([]); disponibilidades = signal<any[]>([]); horas = signal<string[]>([]); cargando = signal(false); mensaje = signal(''); error = signal(false); sesionActiva = signal(false); esPaciente = signal(false);
    ngOnInit() { const usuario = this.auth.obtenerUsuario(); if (!usuario) { this.mostrarError('Para agendar una cita, por favor inicia sesión primero.'); return; } this.sesionActiva.set(true); if (Number(usuario.id_rol) !== 3) { this.mostrarError('La agenda pública está disponible únicamente para cuentas de paciente.'); return; } this.esPaciente.set(true); this.cargar('medicos', this.medicos); this.cargar('salas', this.salas); }
    private cargar(recurso: string, destino: any) { this.http.get<any>(`${this.api}/${recurso}.php`).subscribe({ next: r => destino.set(r.data || []), error: () => this.mostrarError(`No se pudo cargar ${recurso}.`) }); }
    cambiarMedico() { this.disponibilidades.set([]); this.horas.set([]); this.hora.set(''); if (this.idMedico()) this.http.get<any>(`${this.api}/horarios.php?id_medico=${this.idMedico()}`).subscribe({ next: r => { this.disponibilidades.set(r.data || []); this.actualizarHoras(); }, error: () => this.mostrarError('No se pudieron cargar los horarios del médico.') }); }
    actualizarHoras() { this.hora.set(''); const fecha = this.fecha(); if (!fecha) return; const dia = ['DOMINGO', 'LUNES', 'MARTES', 'MIERCOLES', 'JUEVES', 'VIERNES', 'SABADO'][new Date(`${fecha}T12:00:00`).getDay()]; if (!['LUNES', 'MARTES', 'MIERCOLES', 'JUEVES', 'VIERNES'].includes(dia)) { this.horas.set([]); this.mostrarError('Solo puedes seleccionar de lunes a viernes.'); return; } const horarios = this.disponibilidades().filter(d => d.dia_semana === dia); const horas: string[] = []; for (let h = 8; h < 17; h++) { const valor = `${String(h).padStart(2, '0')}:00`; if (horarios.some(d => valor >= d.hora_inicio.slice(0, 5) && valor < d.hora_fin.slice(0, 5))) horas.push(valor); } this.horas.set(horas); if (!horas.length) this.mostrarError('El médico no tiene horarios disponibles para este día.'); else { this.error.set(false); this.mensaje.set(''); } }
    confirmarCita() { if (!this.esPaciente() || !this.idMedico() || !this.idSala() || !this.fecha() || !this.hora()) { this.mostrarError('Completa todos los campos obligatorios.'); return; } this.cargando.set(true); this.http.post<any>(`${this.api}/citas.php`, { id_medico: this.idMedico(), id_sala: this.idSala(), fecha_hora: `${this.fecha()}T${this.hora()}`, motivo_consulta: this.motivoConsulta().trim() }).subscribe({ next: r => { this.cargando.set(false); this.error.set(!r.success); this.mensaje.set(r.success ? `Cita creada correctamente. ID: ${r.id_cita}` : r.mensaje); if (r.success) { this.hora.set(''); this.motivoConsulta.set(''); } }, error: e => { this.cargando.set(false); this.mostrarError(e.error?.mensaje || 'No se pudo crear la cita.'); } }); }
    irALogin() { this.router.navigate(['/login']); }
    private mostrarError(mensaje: string) { this.error.set(true); this.mensaje.set(mensaje); }
}
