import { Injectable, inject } from '@angular/core';
import { CanActivate, Router } from '@angular/router';
import { AuthService } from '../services/auth.service';

@Injectable({
  providedIn: 'root',
})
export class AdminGuard implements CanActivate {
  private auth = inject(AuthService);
  private router = inject(Router);

  canActivate(): boolean {
    const usuario = this.auth.obtenerUsuario();

    // 1. No hay sesión → al login
    if (!usuario) {
      this.router.navigate(['/login-personal']);
      return false;
    }

    // 2. Está logueado pero no es admin (rol 1) → al inicio
    const rol = Number(usuario.id_rol);
    if (rol !== 1) {
      this.router.navigate(['/']);
      return false;
    }

    // 3. Es admin → puede pasar
    return true;
  }
}
