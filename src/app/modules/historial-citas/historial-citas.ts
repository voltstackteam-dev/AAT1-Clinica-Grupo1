import { Component, OnInit, inject, signal } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { HttpClient } from '@angular/common/http';
import { Router } from '@angular/router';
import { AuthService } from '../../services/auth.service';

@Component({ selector: 'app-historial-citas', standalone: true, imports: [CommonModule, FormsModule], templateUrl: './historial-citas.html', styleUrl: './historial-citas.css' })
export class HistorialCitasComponent implements OnInit {
  private http = inject(HttpClient); private auth = inject(AuthService); private router = inject(Router);
  private api = 'http://localhost:8000/api/citas.php';
  misCitas = signal<any[]>([]); cargando = signal(true); mensaje = signal(''); editando = signal<number | null>(null); actualizando = signal<number | null>(null);
  fechaNueva = signal(''); horaNueva = signal(''); hoy = new Date().toISOString().slice(0, 10);
  horas = Array.from({ length: 9 }, (_, indice) => `${String(indice + 8).padStart(2, '0')}:00`);

  ngOnInit(): void { const usuario = this.auth.obtenerUsuario(); if (!usuario || Number(usuario.id_rol) !== 3) { this.router.navigate(['/login']); return; } this.cargarCitas(); }
  cargarCitas(): void { const usuario = this.auth.obtenerUsuario(); this.http.get<any>(`${this.api}?id_usuario=${usuario.id_usuario}`).subscribe({ next: r => { this.misCitas.set(r.data || []); this.cargando.set(false); }, error: () => { this.mensaje.set('No se pudieron cargar tus citas.'); this.cargando.set(false); } }); }
  puedeGestionar(cita: any): boolean { return ['PENDIENTE', 'CONFIRMADA'].includes(cita.estado); }
  cancelar(cita: any): void { if (!confirm(`¿Deseas cancelar la cita #${cita.id_cita}?`)) return; this.actualizarEstado(cita, 'CANCELADA'); }
  prepararReprogramacion(cita: any): void { const [fecha, hora] = String(cita.fecha_hora).split(' '); this.editando.set(cita.id_cita); this.fechaNueva.set(fecha || ''); this.horaNueva.set((hora || '').slice(0, 5)); this.mensaje.set(''); }
  reprogramar(cita: any): void { if (!this.fechaNueva() || !this.horaNueva()) { this.mensaje.set('Selecciona una fecha y hora para reprogramar la cita.'); return; } this.actualizando.set(cita.id_cita); this.http.put<any>(this.api, { id_cita: cita.id_cita, fecha_hora: `${this.fechaNueva()}T${this.horaNueva()}` }).subscribe({ next: respuesta => { this.misCitas.update(citas => citas.map(item => item.id_cita === cita.id_cita ? { ...item, estado: respuesta.estado, fecha_hora: respuesta.fecha_hora } : item)); this.editando.set(null); this.actualizando.set(null); this.mensaje.set(respuesta.mensaje); }, error: error => { this.actualizando.set(null); this.mensaje.set(error.error?.mensaje || 'No se pudo reprogramar la cita.'); } }); }
  private actualizarEstado(cita: any, estado: string): void { this.actualizando.set(cita.id_cita); this.http.put<any>(this.api, { id_cita: cita.id_cita, estado }).subscribe({ next: respuesta => { this.misCitas.update(citas => citas.map(item => item.id_cita === cita.id_cita ? { ...item, estado: respuesta.estado } : item)); this.actualizando.set(null); this.mensaje.set('La cita fue cancelada.'); }, error: error => { this.actualizando.set(null); this.mensaje.set(error.error?.mensaje || 'No se pudo actualizar la cita.'); } }); }
}
