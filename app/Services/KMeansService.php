<?php

namespace App\Services;

class KMeansService
{
    /**
     * Jalankan K-Means Clustering pada data karyawan.
     * Sesuai dengan script Python: StandardScaler + n_init=10 + random_state=42.
     *
     * @param  array  $data           Array of associative arrays, setiap item berisi fitur dan metadata karyawan.
     * @param  int    $k              Jumlah cluster (default: 2).
     * @param  int    $maxIterations  Maksimal iterasi per inisialisasi (default: 100).
     * @param  int    $nInit          Jumlah percobaan inisialisasi acak (default: 10).
     * @return array  Data asli dengan tambahan key 'Kategori' di setiap item.
     */
    public function cluster(array $data, int $k = 2, int $maxIterations = 100, int $nInit = 10): array
    {
        if (empty($data)) {
            return $data;
        }

        if (count($data) < $k) {
            return array_map(function ($i) {
                $i['Kategori'] = '-';
                return $i;
            }, $data);
        }

        $features    = ['Bahagia', 'Etika', 'Responsif', 'Hangat', 'Amanah', 'Semangat', 'Inovatif', 'Loyal'];
        $n           = count($data);
        $dataIndexed = array_values($data);

        // 1. STANDARISASI — StandardScaler (Z-Score), sesuai Python
        $scaled = $this->standardScale($dataIndexed, $features, $n);

        // 2. K-MEANS — n_init=10, random_state=42, sesuai Python
        $bestLabels  = null;
        $bestInertia = PHP_FLOAT_MAX;

        srand(42); // random_state = 42
        $allIndices = range(0, $n - 1);

        for ($init = 0; $init < $nInit; $init++) {
            $shuffled  = $allIndices;
            shuffle($shuffled);

            // Inisialisasi centroid secara acak
            $centroids = [];
            for ($ci = 0; $ci < $k; $ci++) {
                $centroids[$ci] = $scaled[$shuffled[$ci]];
            }

            $labels = array_fill(0, $n, 0);

            for ($iter = 0; $iter < $maxIterations; $iter++) {
                // Assign setiap titik ke centroid terdekat
                $newLabels = [];
                foreach ($scaled as $idx => $point) {
                    $minDist = PHP_FLOAT_MAX;
                    $bestCi  = 0;
                    foreach ($centroids as $ci => $centroid) {
                        $dist = $this->squaredDistance($point, $centroid, $features);
                        if ($dist < $minDist) {
                            $minDist = $dist;
                            $bestCi  = $ci;
                        }
                    }
                    $newLabels[$idx] = $bestCi;
                }

                // Update centroid
                $newCentroids = [];
                for ($ci = 0; $ci < $k; $ci++) {
                    $members = array_keys(array_filter($newLabels, fn($l) => $l === $ci));
                    if (count($members) > 0) {
                        $sum = array_fill_keys($features, 0);
                        foreach ($members as $idx) {
                            foreach ($features as $f) {
                                $sum[$f] += $scaled[$idx][$f];
                            }
                        }
                        foreach ($features as $f) {
                            $newCentroids[$ci][$f] = $sum[$f] / count($members);
                        }
                    } else {
                        $newCentroids[$ci] = $centroids[$ci];
                    }
                }

                if ($labels === $newLabels) break;
                $labels    = $newLabels;
                $centroids = $newCentroids;
            }

            // Hitung Inertia (SSE) — pilih init terbaik
            $inertia = 0;
            foreach ($scaled as $idx => $point) {
                $inertia += $this->squaredDistance($point, $centroids[$labels[$idx]], $features);
            }

            if ($inertia < $bestInertia) {
                $bestInertia = $inertia;
                $bestLabels  = $labels;
            }
        }

        // 3. TENTUKAN KATEGORI — ranking rata-rata nilai asli per cluster (sesuai Python)
        $clusterMeans = [];
        for ($ci = 0; $ci < $k; $ci++) {
            $members   = array_keys(array_filter($bestLabels, fn($l) => $l === $ci));
            $totalMean = 0;
            if (count($members) > 0) {
                foreach ($members as $idx) {
                    $featureSum = 0;
                    foreach ($features as $f) {
                        $featureSum += ($dataIndexed[$idx][$f] ?? 0);
                    }
                    $totalMean += $featureSum / count($features);
                }
                $clusterMeans[$ci] = $totalMean / count($members);
            } else {
                $clusterMeans[$ci] = 0;
            }
        }

        // Cluster dengan rata-rata tertinggi = "Implementasi Core Values Tinggi"
        arsort($clusterMeans);
        $ranks = [];
        $rank  = 1;
        foreach ($clusterMeans as $ci => $mean) {
            $ranks[$ci] = $rank++;
        }

        // 4. Gabungkan hasil ke data asli
        $result = $dataIndexed;
        foreach ($result as $idx => $item) {
            $ci = $bestLabels[$idx];
            $result[$idx]['Kategori'] = ($ranks[$ci] === 1)
                ? 'Implementasi Core Values Tinggi'
                : 'Implementasi Core Values Rendah';
        }

        return $result;
    }

    /**
     * StandardScaler: normalisasi Z-Score per fitur.
     */
    private function standardScale(array $dataIndexed, array $features, int $n): array
    {
        $means = [];
        $stds  = [];

        foreach ($features as $f) {
            $sum      = array_sum(array_column($dataIndexed, $f));
            $mean     = $sum / $n;
            $means[$f] = $mean;

            $variance = 0;
            foreach ($dataIndexed as $row) {
                $variance += pow(($row[$f] ?? 0) - $mean, 2);
            }
            $std       = ($n > 1) ? sqrt($variance / $n) : 1;
            $stds[$f]  = ($std == 0) ? 1 : $std;
        }

        $scaled = [];
        foreach ($dataIndexed as $idx => $row) {
            foreach ($features as $f) {
                $scaled[$idx][$f] = (($row[$f] ?? 0) - $means[$f]) / $stds[$f];
            }
        }

        return $scaled;
    }

    /**
     * Hitung kuadrat jarak Euclidean antara dua titik.
     */
    private function squaredDistance(array $p1, array $p2, array $features): float
    {
        $sum = 0;
        foreach ($features as $f) {
            $sum += pow(($p1[$f] ?? 0) - ($p2[$f] ?? 0), 2);
        }
        return $sum;
    }
}
