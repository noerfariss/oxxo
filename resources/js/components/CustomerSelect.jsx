// components/CustomerSelect.jsx
import React from 'react';
import AsyncSelect from 'react-select/async';
import axios from 'axios';

const CustomerSelect = ({ onChange, defaultValue }) => {
    const baseUrl = import.meta.env.VITE_APP_URL;

    const loadOptions = async (inputValue) => {
        try {
            const response = await axios.get(`${baseUrl}/auth/cashier/customers`, {
                params: { q: inputValue },
            });
            return response.data;
        } catch (error) {
            console.error('Error loading customers:', error);
            return [];
        }
    };

    return (
        <AsyncSelect
            cacheOptions
            loadOptions={loadOptions}
            defaultOptions
            onChange={onChange}
            defaultValue={defaultValue}
            placeholder="Pilih Customer..."
        />
    );
};

export default CustomerSelect;
