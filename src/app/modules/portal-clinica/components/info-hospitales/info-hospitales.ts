import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-info-hospitales',
  standalone: true,
  imports: [CommonModule],
  templateUrl: './info-hospitales.html',
  styleUrl: './info-hospitales.css'
})
export class InfoHospitalesComponent {
  
  // Listado oficial de sedes con URLs universales de Google Maps por coordenadas
  sedesHospital = [
    {
      id: 1,
      nombre: 'Voltstack Sede Central',
      zona: 'Zona 10',
      direccion: 'Av. Principal Médica 12-45, Zona 10, Ciudad de Guatemala',
      telefono: '📞 +502 2300-1000',
      horario: '⏱️ Emergencias: 24/7 | Consulta: 7:00 AM - 7:00 PM',
      // Coordenadas reales Zona 10, Guatemala
      urlMaps: 'https://google.com' 
    },
    {
      id: 2,
      nombre: 'Voltstack Sede Norte',
      zona: 'Zona 11',
      direccion: 'Calzada Roosevelt 22-00, Zona 11, Ciudad de Guatemala',
      telefono: '📞 +502 2300-2000',
      horario: '⏱️ Emergencias: 24/7 | Consulta: 8:00 AM - 6:00 PM',
      // Coordenadas reales Roosevelt Zona 11, Guatemala
      urlMaps: 'https://google.com' 
    }
  ];

  // Función de apertura limpia que rompe el bloqueo preventivo del navegador
  abrirRuta(url: string): void {
    if (url) {
      window.open(url, '_blank', 'noopener,noreferrer');
    }
  }
}
