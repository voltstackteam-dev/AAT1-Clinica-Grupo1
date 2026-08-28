import { ApplicationConfig, provideZonelessChangeDetection } from '@angular/core';
import { provideRouter, withInMemoryScrolling } from '@angular/router';
import { provideHttpClient } from '@angular/common/http';
import { routes } from './app.routes';
import { provideHttpClient, withInterceptors } from '@angular/common/http';
import { authInterceptor } from './interceptors/auth-interceptor';

export const appConfig: ApplicationConfig = {
  providers: [
    // Activa el motor asíncrono nativo por señales estable
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
    ),

    // Habilita el cliente HTTP para consumir las APIs de PHP en XAMPP
    provideHttpClient()
  ]
};