<div class="card mt-4">
    <div class="card-header font-weight-bold">
        <h3>Tâches associées à cette réservation</h3>
    </div>
    <div class="card-body">
        <ul class="list-group">
            @forelse($reservation->taches as $tache)
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <strong>{{ $tache->nom }}</strong> 
                        <p class="mb-0 text-muted text-sm">{{ $tache->description }}</p>
                    </div>
                    
                    <!-- Exemple de badge de statut -->
                    <span class="badge {{ $tache->est_terminee ? 'badge-success' : 'badge-warning' }}">
                        {{ $tache->est_terminee ? 'Terminée' : 'En cours' }}
                    </span>
                </li>
            @empty
                <li class="list-group-item text-center text-muted">
                    Aucune tâche n'est encore planifiée pour cette réservation.
                </li>
            @endforelse
        </ul>
    </div>
</div>