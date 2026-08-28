import { ApplicationConfig, provideZonelessChangeDetection } from '@angular/core'; // <-- CORREGIDO SÉGÚN SUGERENCIA TS2724
import { provideRouter, withInMemoryScrolling } from '@angular/router';
import { routes } from './app.routes';
import { provideHttpClient, withInterceptors } from '@angular/common/http';
import { authInterceptor } from './interceptors/auth-interceptor';

export const appConfig: ApplicationConfig = {
  providers: [
    // CORRECCIÓN DEFINITIVA: Activa el motor asíncrono nativo por señales estable
    provideZonelessChangeDetection(),

    provideHttpClient(
      withInterceptors([
        authInterceptor
      ])
    ),
    
    // Registramos el enrutador con el scroll por fragmentos activo
    provideRouter(
      routes, 
      withInMemoryScrolling({
        anchorScrolling: 'enabled',
        scrollPositionRestoration: 'enabled'
      })
    )
  ]
};
