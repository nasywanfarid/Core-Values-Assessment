import os
os.environ["OPENBLAS_NUM_THREADS"] = "1"
os.environ["OMP_NUM_THREADS"] = "1"

from flask import Flask, request, jsonify
from flask_cors import CORS
import pandas as pd
import numpy as np
from sklearn.preprocessing import StandardScaler
from sklearn.cluster import KMeans

app = Flask(__name__)
CORS(app)

@app.route('/cluster', methods=['POST'])
def cluster():
    try:
        # 1. Terima data JSON dari Laravel
        data = request.get_json()
        if not data:
            return jsonify({"error": "No data provided"}), 400
            
        df = pd.DataFrame(data)

        # 1. Fitur
        fitur = ['Bahagia', 'Etika', 'Responsif', 'Hangat', 'Amanah', 'Semangat', 'Inovatif', 'Loyal']
        
        # 2. Cleaning Data (Memastikan kolom ada, jika tidak ada isi dengan 0)
        for f in fitur:
            if f not in df.columns:
                df[f] = 0
        
        # 3. Standarisasi
        X = df[fitur].fillna(0)
        
        # Jika data kurang dari jumlah cluster, sesuaikan n_clusters
        n_data = len(df)
        n_clusters = 2
        if n_data < n_clusters:
            n_clusters = n_data if n_data > 0 else 1

        if n_data > 0:
            scaler = StandardScaler()
            X_scaled = scaler.fit_transform(X)

            # 4. Model K-Means Clustering
            kmeans = KMeans(n_clusters=n_clusters, random_state=42, n_init=10)
            labels = kmeans.fit_predict(X_scaled)
            df['Cluster'] = labels

            # 5. Penentuan Kategori Otomatis (Logika Anda)
            cluster_means = df.groupby('Cluster')[fitur].mean().mean(axis=1)
            urutan = cluster_means.rank(ascending=False).astype(int)

            # Map urutan ke kategori
            label_map = {}
            for cluster_id, rank in urutan.items():
                if rank == 1:
                    label_map[cluster_id] = 'Implementasi Core Values Tinggi'
                elif rank == 2:
                    label_map[cluster_id] = 'Implementasi Core Values Rendah'
            
            df['Kategori'] = df['Cluster'].map(label_map)
        else:
            df['Cluster'] = 0
            df['Kategori'] = 'N/A'

        # 6. Kirim kembali hasil ke Laravel
        return jsonify(df.to_dict(orient='records'))

    except Exception as e:
        return jsonify({"error": str(e)}), 500

if __name__ == '__main__':
    app.run(port=5000, debug=True)
