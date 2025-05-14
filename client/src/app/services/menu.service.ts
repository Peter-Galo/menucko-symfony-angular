import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { WeeklyMenu } from '../entity/WeeklyMenu';
import { environment } from '../../environments/environment';

@Injectable({
  providedIn: 'root',
})
export class MenuService {
  private apiMenu = environment.apiMenu; // Adjust the URL as necessary

  constructor(private http: HttpClient) {}

  getWeeklyMenu(): Observable<WeeklyMenu> {
    return this.http.get<WeeklyMenu>(this.apiMenu);
  }

  generateWeeklyMenu(): Observable<WeeklyMenu> {
    return this.http.get<WeeklyMenu>(`${this.apiMenu}/generate`);
  }

  downloadWeeklyMenuPdf(): Observable<Blob> {
    return this.http.get(`${this.apiMenu}/pdf`, {
      responseType: 'blob', // Fetch as binary data
    });
  }
}
