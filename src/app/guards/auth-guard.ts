import { inject } from '@angular/core';
import { CanActivateFn, Router } from '@angular/router';
import { AuthService } from '../services/auth.service';

export const adminGuard: CanActivateFn = () => {
  const router = inject(Router);
  const authService = inject(AuthService);

  const token = authService.obtenerToken();
  const usuario = authService.obtenerUsuario();

  // Sin token o sin usuario = no puede entrar
  if (!token || !usuario) {
    return router.createUrlTree(['/login']);
  }

  const rol = Number(usuario.id_rol);

  // Solo administrador (1) y médico (2)
  if (rol === 1 || rol === 2) {
    return true;
  }

  // Paciente u otro rol
  return router.createUrlTree(['/login']);
};