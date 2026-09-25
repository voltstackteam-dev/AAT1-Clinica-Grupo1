import { ComponentFixture, TestBed } from '@angular/core/testing';
import { InfoHospitales } from './info-hospitales';

describe('InfoHospitales', () => {
  let component: InfoHospitales;
  let fixture: ComponentFixture<InfoHospitales>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [InfoHospitales],
    }).compileComponents();

    fixture = TestBed.createComponent(InfoHospitales);
    component = fixture.componentInstance;
    await fixture.whenStable();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
