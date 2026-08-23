import { Component, Input, ViewChild, ElementRef, AfterViewInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterLink } from '@angular/router';

@Component({
  selector: 'app-hero-hospital',
  standalone: true,
  imports: [CommonModule, RouterLink],
  templateUrl: './hero-hospital.html',
  styleUrl: './hero-hospital.css'
})
export class HeroHospital implements AfterViewInit {
  @Input() titulo: string = 'TE SERVIMOS CON EL MEJOR EQUIPO PROFESIONAL';
  @Input() subtitulo: string = 'Cuidado médico de alta complejidad a tu alcance con tecnología de vanguardia y atención humana.';
  @Input() textoBoton: string = 'AGENDA TU CITA';
  @Input() rutaEnlace: string = '/agenda-citas';

  // Capturamos el video de forma segura
  @ViewChild('miVideo') videoElemento!: ElementRef<HTMLVideoElement>;

  ngAfterViewInit(): void {
    if (this.videoElemento) {
      const video = this.videoElemento.nativeElement;
      
      // Forzamos al 100% el silencio para evitar bloqueos del navegador
      video.muted = true;
      video.volume = 0;
      
      // Forzamos al navegador a darle "Play"
      video.play().catch(error => {
        console.log("El navegador pausó el video inicialmente, intentando reanudar...", error);
        // Segundo intento de arranque asistido por si acaso
        setTimeout(() => { video.play(); }, 1000);
      });
    }
  }
}
