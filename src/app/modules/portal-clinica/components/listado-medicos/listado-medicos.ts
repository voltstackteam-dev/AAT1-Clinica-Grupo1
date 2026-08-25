import { Component, OnInit, inject, signal } from '@angular/core';
import { CommonModule } from '@angular/common';
import { Router } from '@angular/router';
import { ClinicaService } from '../../../../services/clinica.service';

@Component({
  selector: 'app-listado-medicos',
  standalone: true,
  imports: [CommonModule],
  templateUrl: './listado-medicos.html',
  styleUrl: './listado-medicos.css'
})
export class ListadoMedicosComponent implements OnInit {
  private clinicaService = inject(ClinicaService);
  private router = inject(Router);

  medicos = signal<any[]>([]);

  ngOnInit(): void {
    this.cargarMedicos();
  }

  cargarMedicos(idEspecialidad: number = 0): void {
    this.clinicaService.getMedicos(idEspecialidad).subscribe({
      next: (res) => {
        if (res.status === 'success') {
          this.medicos.set(res.data);
        }
      },
      error: (err) => console.error('Error al obtener médicos:', err)
    });
  }

  seleccionar(idMedico: number): void {
    this.router.navigate(['/agenda-citas'], { queryParams: { medico: idMedico } });
  }

  // Mapea el nombre de la BD con las fotos existentes en /public/
  getFotoMedico(nombre: string, apellido: string): string {
    const nombreCompleto = `${nombre} ${apellido}`.toLowerCase();
    if (nombreCompleto.includes('ana')) return '/DraSofiaMartinez.png';
    if (nombreCompleto.includes('carlos')) return '/DrCarlosMendoza.png';
    return '/DrAlejandroMendez.png';
  }
}