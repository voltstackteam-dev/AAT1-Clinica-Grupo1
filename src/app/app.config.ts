import { ApplicationConfig, provideZonelessChangeDetection } from '@angular/core'; // ◄ Nombre oficial estable
import { provideRouter } from '@angular/router';
import { provideHttpClient } from '@angular/common/http';
import { routes } from './app.routes';

export const appConfig: ApplicationConfig = {
  providers: [
    // 🚀 Configuración oficial estable Zoneless de Angular
    provideZonelessChangeDetection(),
    provideRouter(routes),
    provideHttpClient()
  ]
};
