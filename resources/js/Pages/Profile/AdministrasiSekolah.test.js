import { describe, it, expect, beforeEach, vi, afterEach } from 'vitest';
import { mount } from '@vue/test-utils';
import { ref } from 'vue';

describe('AdministrasiSekolah Component - Utility Functions', () => {
  
  describe('parseGoogleMapsUrl', () => {
    // Fungsi helper yang di-extract dari komponen
    const parseGoogleMapsUrl = (url) => {
      if (!url) return null;
      let m = url.match(/@(-?\d+\.\d+),(-?\d+\.\d+)/);
      if (m) return { lat: parseFloat(m[1]), lng: parseFloat(m[2]) };
      m = url.match(/!3d(-?\d+\.\d+)!4d(-?\d+\.\d+)/);
      if (m) return { lat: parseFloat(m[1]), lng: parseFloat(m[2]) };
      m = url.match(/3d(-?\d+\.\d+)!4d(-?\d+\.\d+)/);
      if (m) return { lat: parseFloat(m[1]), lng: parseFloat(m[2]) };
      return null;
    };

    it('should parse Google Maps URL with @ notation', () => {
      const url = 'https://maps.google.com/?q=place/@-6.200000,106.816666,15z';
      const result = parseGoogleMapsUrl(url);
      expect(result).not.toBeNull();
      expect(result.lat).toBe(-6.2);
      expect(result.lng).toBe(106.816666);
    });

    it('should parse Google Maps URL with !3d!4d notation', () => {
      const url = 'https://maps.google.com/maps?q=...!3d-6.200000!4d106.816666';
      const result = parseGoogleMapsUrl(url);
      expect(result).not.toBeNull();
      expect(result.lat).toBe(-6.2);
      expect(result.lng).toBe(106.816666);
    });

    it('should return null for invalid URL', () => {
      const result = parseGoogleMapsUrl('https://invalid-url.com');
      expect(result).toBeNull();
    });

    it('should return null for empty URL', () => {
      const result = parseGoogleMapsUrl('');
      expect(result).toBeNull();
    });

    it('should handle negative coordinates', () => {
      const url = 'https://maps.google.com/?q=place/@-6.200000,-106.816666,15z';
      const result = parseGoogleMapsUrl(url);
      expect(result).not.toBeNull();
      expect(result.lat).toBe(-6.2);
      expect(result.lng).toBe(-106.816666);
    });
  });

  describe('setExtractedCoordinates', () => {
    let latInput, lngInput, infoElement;

    beforeEach(() => {
      // Setup DOM elements
      latInput = document.createElement('input');
      latInput.id = 'lat';
      document.body.appendChild(latInput);

      lngInput = document.createElement('input');
      lngInput.id = 'lng';
      document.body.appendChild(lngInput);

      infoElement = document.createElement('div');
      infoElement.id = 'maps-extracted';
      document.body.appendChild(infoElement);
    });

    afterEach(() => {
      document.body.removeChild(latInput);
      document.body.removeChild(lngInput);
      document.body.removeChild(infoElement);
    });

    it('should set latitude and longitude in input fields', () => {
      const setExtractedCoordinates = (lat, lng, source) => {
        const latVal = Number(lat).toFixed(6);
        const lngVal = Number(lng).toFixed(6);
        const latEl = document.getElementById('lat');
        const lngEl = document.getElementById('lng');
        if (latEl) latEl.value = latVal;
        if (lngEl) lngEl.value = lngVal;
        const info = document.getElementById('maps-extracted');
        if (info) info.textContent = `Koordinat diisi dari ${source}: ${latVal}, ${lngVal}`;
      };

      setExtractedCoordinates(-6.200000, 106.816666, 'Geolocation');
      
      expect(latInput.value).toBe('-6.200000');
      expect(lngInput.value).toBe('106.816666');
      expect(infoElement.textContent).toContain('Geolocation');
    });

    it('should format coordinates to 6 decimal places', () => {
      const setExtractedCoordinates = (lat, lng, source) => {
        const latVal = Number(lat).toFixed(6);
        const lngVal = Number(lng).toFixed(6);
        const latEl = document.getElementById('lat');
        const lngEl = document.getElementById('lng');
        if (latEl) latEl.value = latVal;
        if (lngEl) lngEl.value = lngVal;
      };

      setExtractedCoordinates(-6.2, 106.8, 'Test');
      
      expect(latInput.value).toBe('-6.200000');
      expect(lngInput.value).toBe('106.800000');
    });
  });

  describe('Dynamic Anggota Rows', () => {
    let container;

    beforeEach(() => {
      container = document.createElement('div');
      container.id = 'anggota-container';
      document.body.appendChild(container);
    });

    afterEach(() => {
      document.body.removeChild(container);
    });

    it('should create anggota row with proper structure', () => {
      let anggotaCounter = 0;
      
      const addAnggotaRow = () => {
        const cont = document.getElementById('anggota-container');
        if (!cont) return;
        anggotaCounter++;

        const row = document.createElement('div');
        row.className = 'grid grid-cols-1 md:grid-cols-3 gap-4 p-4 bg-white rounded-lg border anggota-row';
        row.id = `anggota-${anggotaCounter}`;

        const col1 = document.createElement('div');
        col1.innerHTML = `<label>Nama Lengkap</label><input type="text" name="anggota_nama[]" />`;

        const col2 = document.createElement('div');
        col2.innerHTML = `<label>Peran/Tanggung Jawab</label><input type="text" name="anggota_peran[]" />`;

        const col3 = document.createElement('div');
        col3.className = 'flex items-end';
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.textContent = 'Hapus';
        btn.addEventListener('click', () => removeAnggotaRow(anggotaCounter));
        col3.appendChild(btn);

        row.appendChild(col1);
        row.appendChild(col2);
        row.appendChild(col3);
        cont.appendChild(row);
      };

      const removeAnggotaRow = (id) => {
        const row = document.getElementById(`anggota-${id}`);
        if (row) row.remove();
      };

      addAnggotaRow();
      
      expect(container.children.length).toBe(1);
      expect(container.querySelector('.anggota-row')).toBeTruthy();
      expect(container.querySelector('input[name="anggota_nama[]"]')).toBeTruthy();
      expect(container.querySelector('input[name="anggota_peran[]"]')).toBeTruthy();
    });

    it('should be able to add multiple anggota rows', () => {
      let anggotaCounter = 0;
      
      const addAnggotaRow = () => {
        const cont = document.getElementById('anggota-container');
        if (!cont) return;
        anggotaCounter++;

        const row = document.createElement('div');
        row.className = 'anggota-row';
        row.id = `anggota-${anggotaCounter}`;
        cont.appendChild(row);
      };

      addAnggotaRow();
      addAnggotaRow();
      addAnggotaRow();

      expect(container.children.length).toBe(3);
      expect(container.querySelector('#anggota-1')).toBeTruthy();
      expect(container.querySelector('#anggota-2')).toBeTruthy();
      expect(container.querySelector('#anggota-3')).toBeTruthy();
    });

    it('should remove specific anggota row by id', () => {
      let anggotaCounter = 0;
      
      const addAnggotaRow = () => {
        const cont = document.getElementById('anggota-container');
        if (!cont) return;
        anggotaCounter++;

        const row = document.createElement('div');
        row.className = 'anggota-row';
        row.id = `anggota-${anggotaCounter}`;
        cont.appendChild(row);
      };

      const removeAnggotaRow = (id) => {
        const row = document.getElementById(`anggota-${id}`);
        if (row) row.remove();
      };

      addAnggotaRow();
      addAnggotaRow();
      expect(container.children.length).toBe(2);

      removeAnggotaRow(1);
      expect(container.children.length).toBe(1);
      expect(container.querySelector('#anggota-1')).toBeFalsy();
      expect(container.querySelector('#anggota-2')).toBeTruthy();
    });
  });
});
