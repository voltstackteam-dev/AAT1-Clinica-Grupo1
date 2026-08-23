import { ComponentFixture, TestBed } from '@angular/core/testing';
import { PortalClinica } from './portal-clinica';

describe('PortalClinica', () => {
  let component: PortalClinica;
  let fixture: ComponentFixture<PortalClinica>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [PortalClinica],
    }).compileComponents();

    fixture = TestBed.createComponent(PortalClinica);
    component = fixture.componentInstance;
    await fixture.whenStable();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
