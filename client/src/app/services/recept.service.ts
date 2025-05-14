import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Recept } from '../entity/Recept';
import { Observable } from 'rxjs';
import { environment } from '../../environments/environment';

@Injectable({
  providedIn: 'root',
})
export class ReceptService {
  private apiRecepty = environment.apiRecepty;

  constructor(private http: HttpClient) {}

  createRecept(recept: Recept): Observable<Recept> {
    return this.http.post<Recept>(this.apiRecepty, recept);
  }

  getGroupedRecepty(): Observable<Record<string, Record<string, Recept[]>>> {
    return this.http.get<Record<string, Record<string, Recept[]>>>(
      this.apiRecepty,
    );
  }
}
