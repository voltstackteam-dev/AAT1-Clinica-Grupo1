import { Component, inject, signal } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { HttpClient } from '@angular/common/http';

@Component({
  selector: 'app-agenda-citas',
  standalone: true,
  imports: [CommonModule, FormsModule],
  templateUrl: './agenda-citas.html',
  styleUrl: './agenda-citas.css'
})
export class AgendaCitasComponent {
  private http = inject(HttpClient);
  private apiUrl = 'http://localhost/api_citas/crear_cita.php';

  // Datos del formulario
  nombrePaciente = signal('');
  dpiPaciente = signal('');
  emailPaciente = signal('');
  telefonoPaciente = signal('');
  idEspecialidad = signal(1);
  fechaCita = signal('');
  horaCita = signal('');

  // Control de estado y mensaje
  exitoRegistro = signal(false);
  codigoGenerado = signal('');
  cargando = signal(false);

  confirmarCita() {
    if (!this.nombrePaciente() || !this.dpiPaciente() || !this.fechaCita() || !this.horaCita()) {
      alert('Por favor completa los campos obligatorios (*)');
      return;
    }

    const payload = {
      nombre_paciente: this.nombrePaciente(),
      dpi_paciente: this.dpiPaciente(),
      email_paciente: this.emailPaciente(),
      telefono_paciente: this.telefonoPaciente(),
      id_especialidad: this.idEspecialidad(),
      fecha_cita: this.fechaCita(),
      hora_cita: this.horaCita()
    };

    this.cargando.set(true);

    this.http.post<any>(this.apiUrl, payload).subscribe({
      next: (res) => {
        this.cargando.set(false);
        if (res.status === 'success') {
          this.exitoRegistro.set(true);
          this.codigoGenerado.set(res.codigo_operacion);
          alert(`¡Cita agendada con éxito! Código: ${res.codigo_operacion}`);
          this.limpiarFormulario();
        } else {
          alert('Error: ' + res.message);
        }
      },
      error: (err) => {
        this.cargando.set(false);
        alert('Error al conectar con el servidor');
        console.error(err);
      }
    });
  }

  limpiarFormulario() {
    this.nombrePaciente.set('');
    this.dpiPaciente.set('');
    this.emailPaciente.set('');
    this.telefonoPaciente.set('');
    this.fechaCita.set('');
    this.horaCita.set('');
  }
}