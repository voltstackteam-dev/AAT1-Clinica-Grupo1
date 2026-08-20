import { Component, Input, Output, EventEmitter } from '@angular/core';
import { CommonModule } from '@angular/common'; // ◄ 1. Importación obligatoria para evitar pantallas en blanco

@Component({
  selector: 'app-footer',
  standalone: true,
  imports: [CommonModule], // ◄ 2. Le damos permisos de leer directivas como *ngIf
  templateUrl: './footer.html',
  styleUrls: ['./footer.css']
})
export class FooterComponent {
  @Input() seccionActual: string = 'inicio';
  @Output() navegar = new EventEmitter<string>();
}
