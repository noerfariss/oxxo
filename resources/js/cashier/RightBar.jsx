import { Button, Card, Col, List, notification } from 'antd'
import React, { useEffect, useState } from 'react'
import { Colors } from '../utils/Colors'
import { CalendarOutlined, PercentageOutlined, RightCircleTwoTone, TagsTwoTone, UserOutlined } from '@ant-design/icons'
import axios from 'axios'
import { getSlug } from '../utils/Helper'

export const RightBar = ({ cart = [], setCart }) => {
    const [api, contextHolder] = notification.useNotification();
    const baseUrl = import.meta.env.VITE_APP_URL;
    const slug = getSlug();

    const [total, setTotal] = useState(0);

    const totalPrice = cart.reduce((total, item) => {
        return total + (item.price * item.quantity);
    }, 0);

    const handleProcess = async () => {
        try {
            const req = await axios.post(`${baseUrl}/auth/cashier/process`, {
                cart: cart,
                slug: slug

            }, {
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            });

            const res = await req.data;
            const data = res.data;
            console.log(data);
            setCart([]);
            openNotificationWithIcon('success');


        } catch (error) {
            console.log(error);
            openNotificationWithIcon('error');
        }

    }

    // const openNotificationWithIcon = (type, title, description) => {
    //     return
    // }
    const openNotificationWithIcon = type  => {
        api[type]({
            message: 'Sukses',
            description:
                'This is the content of the notification. This is the content of the notification. This is the content of the notification.',
        });
    };

    useEffect(() => {
        setTotal(totalPrice);
    }, [cart])

    return (
        <>
            {contextHolder}

            <div style={{ height: '100vh', backgroundColor: Colors.primary, paddingRight: 22, paddingLeft: 22, paddingTop: 14, paddingBottom: 14, display: 'flex', flexDirection: 'column' }}>
                <section style={{ textAlign: 'right' }}>
                    <h3 style={{ padding: 0, margin: 0, fontWeight: 'normal', color: Colors.gray50 }}>Total</h3>
                    <h2 style={{ color: Colors.yellow, fontSize: 32, margin: 0, padding: 0, fontWeight: 500 }}>Rp {total}</h2>
                </section>

                {/* list  */}
                <h3 style={{ marginTop: 22, fontWeight: 500, color: Colors.yellow, letterSpacing: .5 }}><TagsTwoTone /> Detail transaksi</h3>
                <section style={{ flex: 1, height: 320, overflowY: 'scroll' }}>
                    {
                        cart.map((val, i) => {
                            return (
                                <div key={i} style={{ borderWidth: 1, borderColor: Colors.blue700, borderStyle: 'solid', borderRadius: 8, padding: 8, display: 'flex', flexDirection: 'row', justifyContent: 'space-between', cursor: 'pointer', position: 'relative', marginBottom: 12 }}>
                                    <div style={{ flex: 2 }}>
                                        <h5 style={{ margin: 0, padding: 0, color: Colors.gray50, fontWeight: 'bold', fontSize: 11, letterSpacing: .5 }}>{val.category}</h5>
                                        <h4 style={{ margin: 0, padding: 0, color: Colors.gray50, fontWeight: 'normal', letterSpacing: .5 }}>{val.name}</h4>
                                        <div style={{ color: Colors.gray50, fontSize: 11, fontStyle: 'italic' }}>- Rp {val.price}</div>
                                        <div style={{ color: Colors.gray50, fontSize: 11, fontStyle: 'italic' }}>- Uk. 2 x 3.2 Meter</div>
                                    </div>
                                    <div style={{ backgroundColor: Colors.yellow, width: 20, height: 20, fontSize: 14, fontWeight: 'bold', textAlign: "center", borderRadius: 10, marginTop: 18 }}>{val.quantity}</div>
                                    <div style={{ textAlign: 'right', flex: 1 }}>
                                        <h6 style={{ margin: 0, padding: 0, color: Colors.gray50, fontWeight: 'normal', fontSize: 11, letterSpacing: .5 }}>{val.attribute}</h6>
                                        <h6 style={{ margin: 0, padding: 0, color: Colors.gray50, fontWeight: 'bold' }}>Rp {val.subtotal}</h6>
                                    </div>
                                </div>
                            )
                        })
                    }
                </section>

                {/* --------------- tombol & footer ------------------- */}
                <section>
                    <h3 style={{ fontWeight: 500, color: Colors.yellow, letterSpacing: .5, display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
                        <div>
                            <TagsTwoTone /> Informasi
                        </div>
                        <button type='button' style={{ border: 0, background: 'none', color: Colors.gray50, letterSpacing: .5, fontSize: 13, borderWidth: 1, borderStyle: 'solid', borderColor: Colors.gray100, borderRadius: 4 }}>Edit <RightCircleTwoTone /></button>
                    </h3>
                    <section style={{ marginBottom: 22 }}>
                        <div style={{ display: 'flex', flexDirection: 'row', justifyContent: 'space-between', borderBottomWidth: 1, borderBottomStyle: 'dashed', borderColor: Colors.gray100, paddingBottom: 8, paddingTop: 8 }}>
                            <h4 style={{ margin: 0, padding: 0, color: Colors.gray50, fontWeight: 'normal', letterSpacing: .5 }}><PercentageOutlined /> Diskon</h4>
                            <h6 style={{ margin: 0, padding: 0, color: Colors.gray50, fontWeight: 'bold' }}>10%</h6>
                        </div>
                        <div style={{ display: 'flex', flexDirection: 'row', justifyContent: 'space-between', borderBottomWidth: 1, borderBottomStyle: 'dashed', borderColor: Colors.gray100, paddingBottom: 8, paddingTop: 8 }}>
                            <h4 style={{ margin: 0, padding: 0, color: Colors.gray50, fontWeight: 'normal', letterSpacing: .5 }}><CalendarOutlined /> Diambil pada tanggal</h4>
                            <h6 style={{ margin: 0, padding: 0, color: Colors.gray50, fontWeight: 'bold' }}>20 Juni 2025</h6>
                        </div>
                        <div style={{ display: 'flex', flexDirection: 'row', justifyContent: 'space-between', borderBottomWidth: 1, borderBottomStyle: 'dashed', borderColor: Colors.gray100, paddingBottom: 8, paddingTop: 8 }}>
                            <h4 style={{ margin: 0, padding: 0, color: Colors.gray50, fontWeight: 'normal', letterSpacing: .5 }}><UserOutlined /> Member</h4>
                            <h6 style={{ margin: 0, padding: 0, color: Colors.yellow, fontWeight: 'bold', textTransform: 'uppercase' }}>nur faris prastyo</h6>
                        </div>
                    </section>

                    <button
                        type="button"
                        onClick={handleProcess}
                        disabled={total > 0 ? false : true}
                        style={{
                            backgroundColor: total > 0 ? Colors.yellow : Colors.blue100,
                            color: total > 0 ? Colors.black : Colors.gray500,
                            border: 0,
                            width: '100%',
                            padding: 12,
                            borderRadius: 8,
                            marginBottom: 24,
                            cursor: total > 0 ? 'pointer' : 'disabled',
                        }}
                    >
                        Proses
                    </button>
                </section>

            </div>
        </>
    )
}
