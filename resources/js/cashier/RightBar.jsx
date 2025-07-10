import { Button, Card, Col, DatePicker, Drawer, List, Modal, notification } from 'antd'
import React, { useEffect, useState } from 'react'
import { Colors } from '../utils/Colors'
import { CalendarOutlined, DeleteOutlined, PercentageOutlined, RightCircleTwoTone, TagsTwoTone, UserOutlined } from '@ant-design/icons'
import axios from 'axios'
import { getSlug } from '../utils/Helper'
import CustomerSelect from '../components/CustomerSelect'

export const RightBar = ({ cart = [], setCart }) => {
    const [api, contextHolder] = notification.useNotification();
    const baseUrl = import.meta.env.VITE_APP_URL;
    const [open, setOpen] = useState(false);

    const showDrawer = () => {
        setOpen(true);
    };

    const onClose = () => {
        setOpen(false);
    };

    const slug = getSlug();

    const [subtotal, setSubtotal] = useState(0);

    const totalPrice = cart.reduce((total, item) => {
        return total + (item.price * item.quantity);
    }, 0);

    const [information, setInformation] = useState({
        diskon: 0,
        pickup: '-',
        member: '',
        memberID: '',
        memberSaldo: '',
    });

    const diskonPersen = Number(information.diskon) || 0;
    const diskonRupiah = (diskonPersen / 100) * subtotal;
    const grandTotal = subtotal - diskonRupiah;

    const setDatePickup = (date, datestring) => {
        setInformation((prev) => {
            return ({
                ...prev,
                pickup: datestring
            })
        })
    }

    const setDiskon = (val) => {
        setInformation((prev) => {
            return ({
                ...prev,
                diskon: val
            })
        })
    }

    const setSelectedCustomer = (data) => {
        setInformation((prev) => {

            return ({
                ...prev,
                memberID: data.value,
                member: data.label,
                memberSaldo: data.saldo
            })
        })
    }

    const showModal = () => {
        setIsModalOpen(true);
    };

    const [isModalOpen, setIsModalOpen] = useState(false);
    const handleOk = async () => {
        try {
            const req = await axios.post(`${baseUrl}/auth/cashier/process`, {
                cart: cart,
                slug: slug,
                member: information.memberID,
                discount: information.diskon,

            }, {
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            });

            const res = await req.data;
            const data = res.data;

            setIsModalOpen(false);
            setIsModalPrint(true);
            setCart([]);
            setInformation({
                diskon: 0,
                pickup: '-',
                member: '',
                memberID: '',
                memberSaldo: '',
            })
            openNotificationWithIcon('success');


        } catch (error) {
            console.log(error);
            openNotificationWithIcon('error');
        }


    };

    const handleCancel = () => {
        setIsModalOpen(false);
    };

    const [isModalPrint, setIsModalPrint] = useState(false);
    const handlePrintOk = () => {
        setIsModalPrint(false);
    }

    const handleCancelPrint = () => {
        setIsModalPrint(false);
    }

    const openNotificationWithIcon = type => {
        api[type]({
            message: 'Sukses',
            description:
                'Transaksi berhasil dilakukan',
        });
    };

    const removeItem = (item) => {
        const newCart = cart.filter((crt) => crt.id !== item.id);
        setCart(newCart);
    }

    useEffect(() => {
        setSubtotal(totalPrice);
    }, [cart])

    return (
        <>
            {contextHolder}

            <div style={{ height: '100vh', backgroundColor: Colors.primary, paddingRight: 22, paddingLeft: 22, paddingTop: 14, paddingBottom: 14, display: 'flex', flexDirection: 'column' }}>
                <section style={{ textAlign: 'right' }}>
                    <h3 style={{ padding: 0, margin: 0, fontWeight: 'normal', color: Colors.gray50 }}>Total</h3>
                    <h2 style={{ color: Colors.yellow, fontSize: 32, margin: 0, padding: 0, fontWeight: 500 }}>Rp {grandTotal}</h2>
                </section>

                {/* list  */}
                <h3 style={{ marginTop: 22, fontWeight: 500, color: Colors.yellow, letterSpacing: .5 }}><TagsTwoTone /> Detail transaksi</h3>
                <section style={{ flex: 1, height: 320, overflowY: 'scroll' }}>
                    {
                        cart.map((val, i) => {
                            return (
                                <div key={i} style={{ borderWidth: 1, borderColor: Colors.blue700, borderStyle: 'solid', borderRadius: 8, padding: 8, display: 'flex', flexDirection: 'row', justifyContent: 'space-between', position: 'relative', marginBottom: 12 }}>
                                    <div style={{ flex: 2 }}>
                                        <h5 style={{ margin: 0, padding: 0, color: Colors.gray50, fontWeight: 'bold', fontSize: 11, letterSpacing: .5 }}>{val.category}</h5>
                                        <h4 style={{ margin: 0, padding: 0, color: Colors.gray50, fontWeight: 'normal', letterSpacing: .5 }}>{val.name}</h4>
                                        <div style={{ color: Colors.gray50, fontSize: 11, fontStyle: 'italic' }}>- Rp {val.price}</div>
                                        <div style={{ color: Colors.gray50, fontSize: 11, fontStyle: 'italic' }}>- {val.noted}</div>
                                    </div>
                                    <div style={{ backgroundColor: Colors.yellow, width: 20, height: 20, fontSize: 14, fontWeight: 'bold', textAlign: "center", borderRadius: 10, marginTop: 18 }}>{val.quantity}</div>
                                    <div style={{ textAlign: 'right', flex: 1 }}>
                                        <h6 style={{ margin: 0, padding: 0, color: Colors.gray50, fontWeight: 'normal', fontSize: 11, letterSpacing: .5 }}>{val.attribute}</h6>
                                        <h6 style={{ margin: 0, padding: 0, color: Colors.gray50, fontWeight: 'bold' }}>Rp {val.subtotal}</h6>
                                    </div>
                                    <div
                                        onClick={() => removeItem(val)}
                                        style={{ position: 'absolute', right: 0, bottom: 0, backgroundColor: Colors.red, width: 25, height: 25, alignItems: 'center', justifyContent: 'center', display: 'flex', color: 'white', borderRadius: 14, marginBottom: 4, marginRight: 4, cursor: 'pointer' }}>
                                        <DeleteOutlined />
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
                    </h3>


                    <section style={{ marginBottom: 22, cursor: 'pointer' }} onClick={showDrawer}>
                        <div style={{ display: 'flex', flexDirection: 'row', justifyContent: 'space-between', borderBottomWidth: 1, borderBottomStyle: 'dashed', borderColor: Colors.gray100, paddingBottom: 8, paddingTop: 8 }}>
                            <h4 style={{ margin: 0, padding: 0, color: Colors.gray50, fontWeight: 'normal', letterSpacing: .5 }}>Subtotal</h4>
                            <h6 style={{ margin: 0, padding: 0, color: Colors.gray50, fontWeight: 'bold' }}>Rp {subtotal}</h6>
                        </div>
                        <div style={{ display: 'flex', flexDirection: 'row', justifyContent: 'space-between', borderBottomWidth: 1, borderBottomStyle: 'dashed', borderColor: Colors.gray100, paddingBottom: 8, paddingTop: 8 }}>
                            <h4 style={{ margin: 0, padding: 0, color: Colors.gray50, fontWeight: 'normal', letterSpacing: .5 }}><PercentageOutlined /> Diskon</h4>
                            <h6 style={{ margin: 0, padding: 0, color: Colors.gray50, fontWeight: 'bold' }}>{information.diskon}%</h6>
                        </div>
                        <div style={{ display: 'flex', flexDirection: 'row', justifyContent: 'space-between', borderBottomWidth: 1, borderBottomStyle: 'dashed', borderColor: Colors.gray100, paddingBottom: 8, paddingTop: 8 }}>
                            <h4 style={{ margin: 0, padding: 0, color: Colors.gray50, fontWeight: 'normal', letterSpacing: .5 }}><CalendarOutlined /> Diambil pada tanggal</h4>
                            <h6 style={{ margin: 0, padding: 0, color: Colors.gray50, fontWeight: 'bold' }}>{information.pickup}</h6>
                        </div>
                        <div style={{ display: 'flex', flexDirection: 'row', justifyContent: 'space-between', borderBottomWidth: 1, borderBottomStyle: 'dashed', borderColor: Colors.gray100, paddingBottom: 8, paddingTop: 8 }}>
                            <h4 style={{ margin: 0, padding: 0, color: Colors.gray50, fontWeight: 'normal', letterSpacing: .5 }}><UserOutlined /> Customer</h4>
                            <h6 style={{ margin: 0, padding: 0, color: Colors.yellow, fontWeight: 'bold', textTransform: 'uppercase' }}>{information.member}</h6>
                        </div>
                    </section>

                    <button
                        type="button"
                        onClick={() => setIsModalOpen(true)}
                        disabled={(subtotal > 0 && information.member) ? false : true}
                        style={{
                            backgroundColor: (subtotal > 0 && information.member) ? Colors.yellow : Colors.blue100,
                            color: (subtotal > 0 && information.member) ? Colors.black : Colors.gray500,
                            border: 0,
                            width: '100%',
                            padding: 12,
                            borderRadius: 8,
                            marginBottom: 24,
                            cursor: (subtotal > 0 && information.member) ? 'pointer' : 'disabled',
                        }}
                    >
                        Proses
                    </button>
                </section>

                {/* sidebar detail informasi (diskon, customer, tgl diambil) */}
                <Drawer
                    title='Informasi'
                    closable={{ 'aria-label': 'Close Button' }}
                    onClose={onClose}
                    open={open}
                >
                    <div style={{ display: 'flex', flexDirection: 'column', justifyContent: 'space-between', borderBottomWidth: 1, borderBottomStyle: 'dashed', borderColor: Colors.gray100, paddingBottom: 8, paddingTop: 8 }}>
                        <h4 style={{ margin: 0, padding: 0, fontWeight: 'normal', letterSpacing: .5 }}><PercentageOutlined /> Diskon</h4>
                        <input type='text' defaultValue={information.diskon} onChange={(e) => setDiskon(e.target.value)} />
                    </div>
                    <div style={{ display: 'flex', flexDirection: 'column', justifyContent: 'space-between', borderBottomWidth: 1, borderBottomStyle: 'dashed', borderColor: Colors.gray100, paddingBottom: 8, paddingTop: 8 }}>
                        <h4 style={{ margin: 0, padding: 0, fontWeight: 'normal', letterSpacing: .5 }}><CalendarOutlined /> Diambil pada tanggal</h4>
                        <DatePicker onChange={setDatePickup} />
                    </div>
                    <div style={{ display: 'flex', flexDirection: 'column', justifyContent: 'space-between', borderBottomWidth: 1, borderBottomStyle: 'dashed', borderColor: Colors.gray100, paddingBottom: 8, paddingTop: 8 }}>
                        <h4 style={{ margin: 0, padding: 0, fontWeight: 'normal', letterSpacing: .5 }}><UserOutlined /> Customer</h4>
                        <CustomerSelect onChange={setSelectedCustomer} />

                        {
                            information.memberSaldo !== '' && <h6>Saldo: Rp {information.memberSaldo}</h6>
                        }
                    </div>
                </Drawer>

                {/* modal detail proses konfirmasi */}
                <Modal
                    title="Detail Pesanan"
                    closable={{ 'aria-label': 'Custom Close Button' }}
                    open={isModalOpen}
                    okText='Proses Transaksi'
                    cancelText='Batal'
                    onOk={handleOk}
                    onCancel={handleCancel}
                >
                    <section style={{ flex: 1, height: 320, overflowY: 'scroll' }}>
                        {
                            cart.map((val, i) => {
                                return (
                                    <div key={i} style={{ borderWidth: 1, borderColor: Colors.blue700, borderStyle: 'solid', borderRadius: 8, padding: 8, display: 'flex', flexDirection: 'row', justifyContent: 'space-between', position: 'relative', marginBottom: 12 }}>
                                        <div style={{ flex: 2 }}>
                                            <h5 style={{ margin: 0, padding: 0, fontWeight: 'bold', fontSize: 11, letterSpacing: .5 }}>{val.category}</h5>
                                            <h4 style={{ margin: 0, padding: 0, fontWeight: 'normal', letterSpacing: .5 }}>{val.name}</h4>
                                            <div style={{ fontSize: 11, fontStyle: 'italic' }}>- Rp {val.price}</div>
                                            <div style={{ fontSize: 11, fontStyle: 'italic' }}>- Uk. 2 x 3.2 Meter</div>
                                        </div>
                                        <div style={{ backgroundColor: Colors.yellow, width: 20, height: 20, fontSize: 14, fontWeight: 'bold', textAlign: "center", borderRadius: 10, marginTop: 18 }}>{val.quantity}</div>
                                        <div style={{ textAlign: 'right', flex: 1 }}>
                                            <h6 style={{ margin: 0, padding: 0, fontWeight: 'normal', fontSize: 11, letterSpacing: .5 }}>{val.attribute}</h6>
                                            <h6 style={{ margin: 0, padding: 0, fontWeight: 'bold' }}>Rp {val.subtotal}</h6>
                                        </div>
                                    </div>
                                )
                            })
                        }
                    </section>

                    <section style={{ marginBottom: 22, cursor: 'pointer' }} onClick={showDrawer}>
                        <div style={{ display: 'flex', flexDirection: 'row', justifyContent: 'space-between', paddingBottom: 1, paddingTop: 2 }}>
                            <h4 style={{ margin: 0, padding: 0, fontWeight: 'normal', letterSpacing: .5 }}>Subtotal</h4>
                            <h6 style={{ margin: 0, padding: 0, fontWeight: 'bold' }}>Rp {subtotal}</h6>
                        </div>
                        <div style={{ display: 'flex', flexDirection: 'row', justifyContent: 'space-between', borderBottomWidth: 1, borderBottomStyle: 'dashed', borderColor: Colors.gray100, paddingBottom: 1, paddingTop: 2 }}>
                            <h4 style={{ margin: 0, padding: 0, fontWeight: 'normal', letterSpacing: .5 }}><PercentageOutlined /> Diskon</h4>
                            <h6 style={{ margin: 0, padding: 0, fontWeight: 'bold' }}>{information.diskon}%</h6>
                        </div>
                        <div style={{ display: 'flex', flexDirection: 'row', justifyContent: 'space-between', paddingBottom: 1, paddingTop: 2 }}>
                            <h4 style={{ margin: 0, padding: 0, fontWeight: 'normal', letterSpacing: .5 }}>Total</h4>
                            <h6 style={{ margin: 0, padding: 0, fontWeight: 'bold' }}>Rp {grandTotal}</h6>
                        </div>
                        <div style={{ display: 'flex', flexDirection: 'row', justifyContent: 'space-between', borderBottomWidth: 1, borderBottomStyle: 'dashed', borderColor: Colors.gray100, paddingBottom: 1, paddingTop: 2 }}>
                            <h4 style={{ margin: 0, padding: 0, fontWeight: 'normal', letterSpacing: .5 }}><CalendarOutlined /> Diambil pada tanggal</h4>
                            <h6 style={{ margin: 0, padding: 0, fontWeight: 'bold' }}>{information.pickup}</h6>
                        </div>
                        <div style={{ display: 'flex', flexDirection: 'row', justifyContent: 'space-between', borderBottomWidth: 1, borderBottomStyle: 'dashed', borderColor: Colors.gray100, paddingBottom: 1, paddingTop: 2 }}>
                            <h4 style={{ margin: 0, padding: 0, fontWeight: 'normal', letterSpacing: .5 }}><UserOutlined /> Customer</h4>
                            <h6 style={{ margin: 0, padding: 0, color: Colors.primary, fontWeight: 'bold', textTransform: 'uppercase' }}>{information.member}</h6>
                        </div>
                    </section>
                </Modal>

                {/* modal print */}
                <Modal
                    title="Transaksi Berhasil dilakukan"
                    closable={{ 'aria-label': 'Custom Close Button' }}
                    open={isModalPrint}
                    okText='Cetak'
                    cancelText='Tidak'
                    onOk={handlePrintOk}
                    onCancel={handleCancelPrint}
                >
                    <p>Ingin Cetak Bill?</p>
                </Modal>
            </div>
        </>
    )
}
