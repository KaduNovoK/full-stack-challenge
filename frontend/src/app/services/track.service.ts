import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from '../../environments/environment';

export interface Track {
  id: number;
  isrc: string;
  title: string;
  artists: string;
  duration: string;
  thumb_url: string;
  preview_url: string;
  spotify_url: string;
  release_date: string;
  is_available_in_br: boolean;
  created_at: string;
  updated_at: string;
}

@Injectable({
  providedIn: 'root'
})
export class TrackService {
  private apiUrl = `${environment.apiBaseUrl}/track`;

  constructor(private http: HttpClient) {}

  getTracks(): Observable<Track[]> {
    return this.http.get<Track[]>(this.apiUrl);
  }
}
