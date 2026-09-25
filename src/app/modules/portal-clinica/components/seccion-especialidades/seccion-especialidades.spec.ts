import { ComponentFixture, TestBed } from '@angular/core/testing';
import { SeccionEspecialidades } from './seccion-especialidades';

describe('SeccionEspecialidades', () => {
  let component: SeccionEspecialidades;
  let fixture: ComponentFixture<SeccionEspecialidades>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [SeccionEspecialidades],
    }).compileComponents();

    fixture = TestBed.createComponent(SeccionEspecialidades);
    component = fixture.componentInstance;
    await fixture.whenStable();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
