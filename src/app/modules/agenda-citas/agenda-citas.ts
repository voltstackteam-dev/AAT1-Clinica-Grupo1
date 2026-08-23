import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { ActivatedRoute, Router } from '@angular/router';

@Component({
  selector: 'app-agenda-citas',
  standalone: true,
  imports: [CommonModule, FormsModule],
  templateUrl: './agenda-citas.html',
  styleUrl: './agenda-citas.css'
})
export class AgendaCitasComponent implements OnInit {
  
  // Modelo de datos para el formulario
  datosCita = {
    nombrePaciente: '',
    dpi: '',
    correo: '',
    telefono: '',
    especialidad: 'Medicina General',
    medicoId: null as number | null,
    fecha: '',
    hora: ''
  };

  // Listado para validación de especialidades
  especialidades = ['Medicina General', 'Cardiología', 'Pediatría', 'Traumatología', 'Neurología'];
  
  exitoRegistro: boolean = false;

  constructor(private route: ActivatedRoute, private router: Router) {}

  ngOnInit(): void {
    // Captura reactiva del parámetro ID del médico enviado por la URL
    this.route.params.subscribe(params => {
      if (params['id']) {
        this.datosCita.medicoId = +params['id'];
        this.autoAsignarEspecialidad(this.datosCita.medicoId);
      }
    });
  }

  // Asigna automáticamente la rama médica según el doctor elegido en el Home
  autoAsignarEspecialidad(id: number): void {
    const mapaEspecialidades: { [key: number]: string } = {
      1: 'Cardiología',
      2: 'Pediatría',
      3: 'Traumatología',
      4: 'Neurología',
      5: 'Medicina General'
    };
    this.datosCita.especialidad = mapaEspecialidades[id] || 'Medicina General';
  }

  // Procesa el envío del formulario y simula la inserción en la Base de Datos
  procesarCita(): void {
    this.exitoRegistro = true;
    
    // Simulación: En producción aquí se enviaría el objeto 'this.datosCita' vía HTTP POST
    setTimeout(() => {
      this.exitoRegistro = false;
      this.router.navigate(['/']); // Redirige al inicio tras confirmar
    }, 4000);
  }
}
