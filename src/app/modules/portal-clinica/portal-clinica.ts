import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ActivatedRoute, Router } from '@angular/router';

import { HeroHospital } from './components/hero-hospital/hero-hospital';
import { SeccionServicios } from './components/seccion-servicios/seccion-servicios';
import { SeccionEspecialidades } from './components/seccion-especialidades/seccion-especialidades';
import { ListadoMedicosComponent } from './components/listado-medicos/listado-medicos';
import { InfoHospitalesComponent } from './components/info-hospitales/info-hospitales';

@Component({
  selector: 'app-portal-clinica',
  standalone: true,
  imports: [
    CommonModule,
    HeroHospital,
    SeccionServicios,
    SeccionEspecialidades,
    ListadoMedicosComponent,
    InfoHospitalesComponent
  ],
  templateUrl: './portal-clinica.html',
  styleUrl: './portal-clinica.css'
})
export class PortalClinicaComponent implements OnInit {
  
  // Controla cuál bloque se renderiza en la pantalla ('inicio', 'servicios', 'especialidades', 'medicos', 'hospitales')
  seccionActiva: string = 'inicio';

  constructor(private route: ActivatedRoute, private router: Router) {}

  ngOnInit(): void {
    // Escucha activamente los cambios de parámetros de la URL para conmutar la vista
    this.route.queryParams.subscribe(params => {
      this.seccionActiva = params['vista'] || 'inicio';
      
      // Forzar al navegador a subir al tope superior al cambiar de sección
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });
  }

  redirigirCita(idMedico: number): void {
    this.router.navigate(['/agenda-citas', idMedico]);
  }
}
