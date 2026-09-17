import Swal, { SweetAlertIcon } from 'sweetalert2';

let cola: Promise<unknown> = Promise.resolve();

function encolar<T>(accion: () => Promise<T>): Promise<T> {
  const resultado = cola.then(accion, accion);
  cola = resultado.catch(() => undefined);
  return resultado;
}

export function mostrarAviso(
  texto: string,
  icono: SweetAlertIcon = 'info',
): Promise<void> {
  if (!texto) {
    return Promise.resolve();
  }

  const titulos: Record<SweetAlertIcon, string> = {
    success: 'Operación realizada',
    error: 'No se pudo completar',
    warning: 'Revisa los datos',
    info: 'Información',
    question: 'Confirmación',
  };

  return encolar(async () => {
    await Swal.fire({
      icon: icono,
      title: titulos[icono],
      text: texto,
      confirmButtonText: 'Aceptar',
      confirmButtonColor: '#1e3a8a',
    });
  });
}

export function notificar(
  texto: string,
  icono: SweetAlertIcon = 'info',
): string {
  void mostrarAviso(texto, icono);
  return texto;
}

export function confirmar(texto: string): Promise<boolean> {
  return encolar(async () => {
    const resultado = await Swal.fire({
      icon: 'question',
      title: '¿Deseas continuar?',
      text: texto,
      showCancelButton: true,
      confirmButtonText: 'Sí, continuar',
      cancelButtonText: 'Volver',
      confirmButtonColor: '#1e3a8a',
      focusCancel: true,
    });

    return resultado.isConfirmed;
  });
}
