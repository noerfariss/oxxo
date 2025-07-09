import React, { useEffect, useState } from 'react';
import { Layout, Row, Col } from 'antd';
import Header from './Header';
import Category from './Category';
import { RightBar } from './RightBar';
import Items from './Items';
import { Colors } from '../utils/Colors';
import axios from 'axios';
import { getSlug } from '../utils/Helper';

export default function Cashier() {
    const slug = getSlug();
    const baseUrl = import.meta.env.VITE_APP_URL;
    const [search, setSearch] = useState('');
    const [searchCategory, setSearchCategory] = useState('');

    const [items, setItems] = useState([]);
    const [categories, setCategories] = useState([]);

    const [cart, setCart] = useState([]);

    const getCategories = async () => {
        try {
            const req = await axios.get(`${baseUrl}/auth/cashier/categories`, {
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            });

            const res = await req.data;
            const data = res.data;

            setCategories(data);

        } catch (error) {
            console.log(error);

        }
    }

    const getItems = async () => {
        try {
            const req = await axios.post(`${baseUrl}/auth/cashier/items`, {
                search: search,
                category: searchCategory,
                slug: slug
            }, {
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            });

            const res = await req.data;
            const data = res.data;
            setItems(data);

        } catch (error) {
            console.log(error);

        }
    }

    const handleAddToCart = (newItem) => {
        setCart(prev => {
            const exist = prev.find(item =>
                item.id === newItem.id && item.attribute === newItem.attribute
            );

            if (exist) {
                return prev.map(item =>
                    item.id === newItem.id && item.attribute === newItem.attribute
                        ? {
                            ...item,
                            quantity: item.quantity + 1,
                            subtotal: item.price * (item.quantity + 1),
                        }
                        : item
                );
            } else {
                return [...prev, newItem];
            }
        });
    }

    useEffect(() => {
        getCategories();
    }, [])

    useEffect(() => {
        getItems();
    }, [search, searchCategory])

    return (
        <Layout style={{ minHeight: '100vh' }}>

            <Row>
                <Col xs={24} md={16} xl={18} style={{ display: 'flex', flexDirection: 'column' }}>
                    <div style={{ backgroundColor: 'white', boxShadow: '0 7px 7px #ECEFF1', paddingBottom: 12, paddingLeft: 12, paddingRight: 12 }}>
                        <Header />
                        <Category datas={categories} onChangeSearch={(e) => setSearch(e)} onChangeCategory={(e) => setSearchCategory(e)} searchCategory={searchCategory} />
                    </div>
                    <div style={{ paddingLeft: 12, paddingRight: 12, marginTop: 16, flex: 1 }}>
                        <Items datas={items} setAddToCart={(e) => handleAddToCart(e)} />
                    </div>
                    <div style={{ marginTop: 12, boxShadow: '0 0 7px #ECEFF1', backgroundColor: Colors.blue100, paddingLeft: 12, paddingRight: 12, paddingTop: 22, flex: 1, display: 'flex', flexDirection: 'row', alignItems: 'start', justifyContent: 'space-between' }}>

                    </div>
                </Col>
                <Col xs={24} md={8} xl={6}>
                    <RightBar cart={cart} setCart={(e) => setCart(e)} />
                </Col>
            </Row>
        </Layout>
    );
}
