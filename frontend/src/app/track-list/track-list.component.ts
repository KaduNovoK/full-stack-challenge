import { Component, OnInit } from '@angular/core';
import { TrackService, Track } from '../services/track.service';

@Component({
  selector: 'app-track-list',
  templateUrl: './track-list.component.html',
  styleUrls: ['./track-list.component.scss']
})
export class TrackListComponent implements OnInit {
  tracks: Track[] = [];
  loading = true;
  error: string | null = null;

  constructor(private trackService: TrackService) {}

  ngOnInit(): void {
    this.trackService.getTracks().subscribe({
      next: (data) => {
        this.tracks = data;
        this.loading = false;
      },
      error: () => {
        this.error = 'Erro ao carregar as faixas.';
        this.loading = false;
      }
    });
  }
}
