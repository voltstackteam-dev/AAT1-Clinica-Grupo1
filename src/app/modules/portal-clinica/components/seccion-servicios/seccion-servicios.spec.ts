import { ComponentFixture, TestBed } from '@angular/core/testing';
import { SeccionServicios } from './seccion-servicios';

describe('SeccionServicios', () => {
  let component: SeccionServicios;
  let fixture: ComponentFixture<SeccionServicios>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [SeccionServicios],
    }).compileComponents();

    fixture = TestBed.createComponent(SeccionServicios);
    component = fixture.componentInstance;
    await fixture.whenStable();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
