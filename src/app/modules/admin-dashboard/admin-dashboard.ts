import { Component, OnInit, inject, signal } from '@angular/core';
import { CommonModule } from '@angular/common';
import { HttpClient } from '@angular/common/http';
import { FormsModule } from '@angular/forms';

export interface CitaClinica {
  id_cita: number;
  codigo_operacion: string;
  nombre_paciente: string;
  dpi_paciente: string;
  email_paciente: string;
  telefono_paciente: string;
  fecha_cita: string;
  hora_cita: string;
  motivo_consulta: string;
  observaciones: string | null;
  estado_cita: string;
  nombre_medico: string;
  nombre_especialidad: string;
}

@Component({
  selector: 'app-admin-dashboard',
  standalone: true,
  imports: [CommonModule, FormsModule],
  templateUrl: './admin-dashboard.html',
  styleUrl: './admin-dashboard.css'
})
export class AdminDashboardComponent implements OnInit {
  private http = inject(HttpClient);
  private urlGet = 'http://localhost/api_citas/get_citas.php';
  private urlActualizar = 'http://localhost/api_citas/actualizar_cita.php';

  citas = signal<CitaClinica[]>([]);
  filtroActual = signal<string>('todas'); // 'todas', 'pendiente', 'confirmada'
  cargando = signal(false);

  ngOnInit() {
    this.cargarCitas();
  }

  cargarCitas() {
    this.cargando.set(true);
    this.http.get<any>(this.urlGet).subscribe({
      next: (res) => {
        this.cargando.set(false);
        if (res.status === 'success') {
          this.citas.set(res.data);
        }
      },
      error: (err) => {
        this.cargando.set(false);
        console.error('Error al cargar citas:', err);
      }
    });
  }

// Filtrar según el botón seleccionado en la parte superior
citasFiltradas() {
  const filtro = this.filtroActual();
  if (filtro === 'pendiente') {
    return this.citas().filter(c => c.estado_cita.toLowerCase() === 'pendiente');
  }
  if (filtro === 'confirmada') {
    return this.citas().filter(c => c.estado_cita.toLowerCase() === 'confirmada');
  }
  if (filtro === 'finalizada') {
    return this.citas().filter(c => c.estado_cita.toLowerCase() === 'finalizada');
  }
  if (filtro === 'finalizada') {
    return this.citas().filter(c => c.estado_cita.toLowerCase() === 'finalizada');
  
  }
  return this.citas();
}

  setFiltro(filtro: string) {
    this.filtroActual.set(filtro);
  }

  autorizarCita(id_cita: number) {
    this.http.post<any>(this.urlActualizar, { id_cita, accion: 'autorizar' }).subscribe({
      next: (res) => {
        if (res.status === 'success') {
          alert('¡Cita autorizada con éxito!');
          this.cargarCitas();
        }
      },
      error: (err) => alert('Error al autorizar cita')
    });
  }

  cancelarCita(id_cita: number) {
    if (confirm('¿Estás seguro de denegar o cancelar esta cita?')) {
      this.http.post<any>(this.urlActualizar, { id_cita, accion: 'cancelar' }).subscribe({
        next: (res) => {
          if (res.status === 'success') {
            alert('Cita cancelada');
            this.cargarCitas();
          }
        },
        error: (err) => alert('Error al cancelar cita')
      });
    }
  }

  modificarNotas(cita: CitaClinica) {
    const nuevaNota = prompt('Ingresa las observaciones clínicas / diagnóstico preventivo:', cita.observaciones || '');
    if (nuevaNota !== null) {
      this.http.post<any>(this.urlActualizar, { 
        id_cita: cita.id_cita, 
        accion: 'notas', 
        observaciones: nuevaNota 
      }).subscribe({
        next: (res) => {
          if (res.status === 'success') {
            this.cargarCitas();
          }
        },
        error: (err) => alert('Error al actualizar notas')
      });
    }
  }
}